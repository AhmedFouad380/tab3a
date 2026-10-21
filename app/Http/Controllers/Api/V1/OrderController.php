<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\CreatePreOrderRequest;
use App\Http\Requests\Api\V1\CreateSelfPrintOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\FinishingOption;
use App\Models\KioskMachine;
use App\Models\KioskSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PricingRule;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class OrderController extends BaseApiController
{
    /**
     * Create & Checkout Instant Self-Printing Order
     */
    public function createSelfPrintOrder(CreateSelfPrintOrderRequest $request): JsonResponse
    {
        $user = $request->user();

        // 1. Resolve data from KioskSession if session_token is provided
        $session = null;
        if ($request->filled('session_token')) {
            $session = KioskSession::where('session_token', $request->session_token)->firstOrFail();
            $kiosk = $session->kiosk;
            $filePath = $request->input('file_path', $session->file_path);
            $originalFileName = $request->input('original_file_name', $session->file_name);
            $fileExtension = $request->input('file_extension', pathinfo($originalFileName ?? '', PATHINFO_EXTENSION) ?: 'pdf');
            $fileSizeBytes = (int) $request->input('file_size_bytes', $session->file_size_bytes ?: 0);
            $detectedPageCount = (int) $request->input('detected_page_count', $session->detected_page_count ?: 1);
            $copies = (int) $request->input('copies_count', $session->copies_count ?: 1);
            $paperSize = $request->input('paper_size', $session->paper_size ?: 'A4');
            $colorMode = $request->input('color_mode', $session->color_mode ?: 'black_and_white');
            $sideMode = $request->input('side_mode', $session->side_mode ?: 'single_sided');
        } else {
            $kiosk = KioskMachine::findOrFail($request->kiosk_machine_id);
            $filePath = $request->file_path;
            $originalFileName = $request->original_file_name;
            $fileExtension = $request->input('file_extension', pathinfo($originalFileName ?? '', PATHINFO_EXTENSION) ?: 'pdf');
            $fileSizeBytes = (int) $request->input('file_size_bytes', 0);
            $detectedPageCount = (int) $request->detected_page_count;
            $copies = (int) ($request->copies_count ?: 1);
            $paperSize = $request->input('paper_size', 'A4');
            $colorMode = $request->input('color_mode', 'black_and_white');
            $sideMode = $request->input('side_mode', 'single_sided');
        }

        $pagesToPrint = $detectedPageCount;
        if ($request->filled('page_range_selection') && $request->page_range_selection !== 'all') {
            $range = $request->page_range_selection;
            if (str_contains($range, '-')) {
                $parts = explode('-', $range);
                $pagesToPrint = max(1, ((int) $parts[1] - (int) $parts[0] + 1));
            }
        }

        $sheetsPerCopy = $sideMode === 'double_sided' ? (int) ceil($pagesToPrint / 2) : $pagesToPrint;
        $totalSheets = $sheetsPerCopy * $copies;

        // 1. فحص الحد الأقصى لعدد الأوراق المسموح به للطباعة الفورية
        $maxAllowedSheets = $kiosk->getEffectiveMaxSheets();
        if ($totalSheets > $maxAllowedSheets) {
            return $this->error(
                $this->getLocale() === 'en'
                    ? "The order exceeds the maximum sheets allowed for instant printing ({$maxAllowedSheets} sheets). Please use pre-order for larger jobs."
                    : "يتجاوز الطلب الحد الأقصى للأوراق المسموح به في الطباعة الفورية ({$maxAllowedSheets} ورقة). فضلاً استخدم خدمة الطلب المسبق للكميات الكبيرة.",
                422
            );
        }

        // 2. فحص رصيد الورق الفعلي المتاح في درج الماكينة
        $availableInTray = $paperSize === 'A3' ? $kiosk->paper_tray_a3_sheets : $kiosk->paper_tray_a4_sheets;
        if ($totalSheets > $availableInTray) {
            return $this->error(
                $this->getLocale() === 'en'
                    ? "The machine does not have enough {$paperSize} paper ({$availableInTray} sheets available, required: {$totalSheets})."
                    : "الماكينة لا تحتوي على ورق كافٍ حالياً ({$availableInTray} ورقة متاحة، والمطلوب {$totalSheets} ورقة).",
                422
            );
        }

        // 3. فحص الحد الأقصى لحجم الملف
        $maxFileSizeMb = $kiosk->getEffectiveMaxFileSizeMb();
        if ($fileSizeBytes > 0 && ($fileSizeBytes > $maxFileSizeMb * 1024 * 1024)) {
            return $this->error(
                $this->getLocale() === 'en'
                    ? "File size exceeds the maximum allowed for instant printing ({$maxFileSizeMb} MB)."
                    : "حجم الملف يتجاوز الحد الأقصى المسموح به للطباعة الفورية ({$maxFileSizeMb} ميجابايت).",
                422
            );
        }

        $rule = PricingRule::where('paper_size', $paperSize)
            ->where('color_mode', $colorMode)
            ->where('side_mode', $sideMode)
            ->where('is_active', true)
            ->first();

        $unitPrice = $rule ? (float) $rule->price_per_page : ($colorMode === 'color' ? 1.50 : 0.50);
        $subtotal = ($unitPrice * $pagesToPrint) * $copies;

        // 4. تطبيق الحد الأدنى لسعر العملية (Minimum Order Price)
        $minOrderPrice = $kiosk->getEffectiveMinOrderPrice();
        if ($subtotal < $minOrderPrice) {
            $subtotal = $minOrderPrice;
        }

        $taxAmount = round($subtotal * 0.15, 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        if ($request->payment_method === 'wallet' && $user->wallet_balance < $totalAmount) {
            $errMsg = $this->getLocale() === 'en' ? 'Insufficient wallet balance' : 'رصيد المحفظة غير كافٍ لإتمام عملية الطباعة';

            return $this->error($errMsg, 400);
        }

        return DB::transaction(function () use ($request, $user, $kiosk, $session, $originalFileName, $filePath, $fileExtension, $fileSizeBytes, $detectedPageCount, $pagesToPrint, $copies, $sideMode, $paperSize, $colorMode, $totalSheets, $unitPrice, $subtotal, $taxAmount, $totalAmount) {
            $orderNumber = 'TAB-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'order_type' => 'self_printing',
                'is_teacher' => $request->boolean('is_teacher'),
                'branch_id' => $kiosk->branch_id,
                'kiosk_machine_id' => $kiosk->id,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'printer_job_id' => 'JOB-'.uniqid(),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'original_file_name' => $originalFileName,
                'file_path' => $filePath,
                'file_extension' => $fileExtension,
                'file_size_bytes' => $fileSizeBytes,
                'detected_page_count' => $detectedPageCount,
                'pages_to_print_count' => $pagesToPrint,
                'page_range_selection' => $request->page_range_selection ?? 'all',
                'paper_size' => $paperSize,
                'color_mode' => $colorMode,
                'side_mode' => $sideMode,
                'copies_count' => $copies,
                'total_sheets_needed' => $totalSheets,
                'unit_price_per_page' => $unitPrice,
                'total_item_price' => $subtotal,
            ]);

            if ($paperSize === 'A3') {
                $kiosk->decrement('paper_tray_a3_sheets', min($kiosk->paper_tray_a3_sheets, $totalSheets));
            } else {
                $kiosk->decrement('paper_tray_a4_sheets', min($kiosk->paper_tray_a4_sheets, $totalSheets));
            }

            $kiosk->update([
                'is_paper_low' => $kiosk->paper_tray_a4_sheets < 50,
                'is_paper_empty' => $kiosk->paper_tray_a4_sheets == 0,
            ]);

            // Sync with Kiosk Screen: Update status to 'printing'
            if (! $session) {
                if ($request->filled('session_token')) {
                    $session = KioskSession::where('session_token', $request->session_token)->first();
                } else {
                    $session = KioskSession::where('kiosk_machine_id', $kiosk->id)
                        ->whereIn('status', ['waiting_scan', 'connected', 'file_uploaded', 'awaiting_payment'])
                        ->latest()
                        ->first();
                }
            }

            if ($session) {
                $session->update([
                    'status' => 'printing',
                    'order_id' => $order->id,
                    'completed_at' => now(),
                ]);
            }

            if ($request->payment_method === 'wallet') {
                $before = $user->wallet_balance;
                $user->decrement('wallet_balance', $totalAmount);
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'payment',
                    'amount' => $totalAmount,
                    'balance_before' => $before,
                    'balance_after' => $user->wallet_balance,
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'description' => [
                        'ar' => "دفع طباعة ذاتية لطلب {$order->order_number}",
                        'en' => "Self-print payment for order {$order->order_number}",
                    ],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'transaction_id' => 'TXN-'.strtoupper(uniqid()),
                'gateway' => $request->payment_method,
                'amount' => $totalAmount,
                'currency' => 'SAR',
                'status' => 'successful',
                'paid_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'completed',
                'comment' => [
                    'ar' => 'تمت الطباعة بنجاح عبر الماكينة الفورية',
                    'en' => 'Printed successfully via kiosk machine',
                ],
                'created_by_type' => 'kiosk_agent',
            ]);

            $message = $this->getLocale() === 'en'
                ? 'Print job executed successfully'
                : 'تم إرسال وطباعة الملف بنجاح';

            return $this->success(new OrderResource($order->load(['branch', 'kiosk', 'items'])), $message);
        });
    }

    /**
     * Create Pre-Order (Branch Printing)
     */
    public function createPreOrder(CreatePreOrderRequest $request): JsonResponse
    {
        $user = $request->user();

        $pagesToPrint = (int) $request->detected_page_count;
        $copies = (int) $request->copies_count;
        $sideMode = $request->side_mode;
        $paperSize = $request->paper_size;
        $colorMode = $request->color_mode;

        if ($request->filled('page_range_selection') && $request->page_range_selection !== 'all') {
            $range = $request->page_range_selection;
            if (str_contains($range, '-')) {
                $parts = explode('-', $range);
                $pagesToPrint = max(1, ((int) $parts[1] - (int) $parts[0] + 1));
            }
        }

        $sheetsPerCopy = $sideMode === 'double_sided' ? (int) ceil($pagesToPrint / 2) : $pagesToPrint;
        $totalSheets = $sheetsPerCopy * $copies;

        $rule = PricingRule::where('paper_size', $paperSize)
            ->where('color_mode', $colorMode)
            ->where('side_mode', $sideMode)
            ->where('is_active', true)
            ->first();

        $unitPrice = $rule ? (float) $rule->price_per_page : ($colorMode === 'color' ? 1.50 : 0.50);
        $printSubtotal = ($unitPrice * $pagesToPrint) * $copies;

        $finishingPrice = 0.00;
        if ($request->filled('finishing_option_id')) {
            $finishing = FinishingOption::find($request->finishing_option_id);
            if ($finishing) {
                $finishingPrice = ((float) $finishing->base_price) * $copies;
            }
        }

        $subtotal = $printSubtotal + $finishingPrice;
        $taxAmount = round($subtotal * 0.15, 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        if ($request->payment_method === 'wallet' && $user->wallet_balance < $totalAmount) {
            $errMsg = $this->getLocale() === 'en' ? 'Insufficient wallet balance' : 'رصيد المحفظة غير كافٍ لإتمام الطلب';

            return $this->error($errMsg, 400);
        }

        return DB::transaction(function () use ($request, $user, $pagesToPrint, $copies, $sideMode, $paperSize, $colorMode, $totalSheets, $unitPrice, $finishingPrice, $subtotal, $taxAmount, $totalAmount) {
            $orderNumber = 'TAB-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'order_type' => 'pre_order',
                'is_teacher' => $request->boolean('is_teacher'),
                'branch_id' => $request->branch_id,
                'scheduled_pickup_at' => $request->scheduled_pickup_at,
                'status' => 'pending',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'user_notes' => $request->user_notes,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'original_file_name' => $request->original_file_name,
                'file_path' => $request->file_path,
                'file_extension' => $request->file_extension,
                'file_size_bytes' => $request->file_size_bytes,
                'detected_page_count' => $request->detected_page_count,
                'pages_to_print_count' => $pagesToPrint,
                'page_range_selection' => $request->page_range_selection ?? 'all',
                'paper_size' => $paperSize,
                'color_mode' => $colorMode,
                'side_mode' => $sideMode,
                'copies_count' => $copies,
                'finishing_option_id' => $request->finishing_option_id,
                'finishing_price' => $finishingPrice,
                'total_sheets_needed' => $totalSheets,
                'unit_price_per_page' => $unitPrice,
                'total_item_price' => $subtotal,
            ]);

            if ($request->payment_method === 'wallet') {
                $before = $user->wallet_balance;
                $user->decrement('wallet_balance', $totalAmount);
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'payment',
                    'amount' => $totalAmount,
                    'balance_before' => $before,
                    'balance_after' => $user->wallet_balance,
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'description' => [
                        'ar' => "دفع طباعة مسبقة لطلب {$order->order_number}",
                        'en' => "Pre-order payment for order {$order->order_number}",
                    ],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'transaction_id' => 'TXN-'.strtoupper(uniqid()),
                'gateway' => $request->payment_method,
                'amount' => $totalAmount,
                'currency' => 'SAR',
                'status' => 'successful',
                'paid_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'comment' => [
                    'ar' => 'تم استلام الطلب وبانتظار بدء التجهيز بالفرع',
                    'en' => 'Order received, pending branch processing',
                ],
                'created_by_type' => 'user',
            ]);

            $message = $this->getLocale() === 'en'
                ? 'Pre-order created successfully. You will be notified once ready for pickup.'
                : 'تم إنشاء الطلب بنجاح، سيتم إشعارك فور اكتمال التجهيز بالفرع.';

            return $this->success(new OrderResource($order->load(['branch', 'items'])), $message);
        });
    }

    /**
     * Get User Orders List (My Orders)
     */
    public function myOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Order::with(['branch', 'kiosk', 'items'])
            ->where('user_id', $user->id)
            ->latest();

        if ($request->type === 'active') {
            $query->whereIn('status', ['pending', 'processing', 'printing', 'ready_for_pickup']);
        } elseif ($request->type === 'completed') {
            $query->whereIn('status', ['completed', 'cancelled']);
        }

        $orders = $query->paginate(15);

        return $this->success(OrderResource::collection($orders), 'Success', 200, [
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'total' => $orders->total(),
        ]);
    }

    /**
     * Show Single Order Details
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::with(['branch', 'kiosk', 'items.finishingOption', 'statusHistories', 'payments'])
            ->where('user_id', $user->id)
            ->find($id);

        if (! $order) {
            return $this->error($this->getLocale() === 'en' ? 'Order not found' : 'الطلب غير موجود', 404);
        }

        return $this->success(new OrderResource($order));
    }

    /**
     * Cancel Order
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)->find($id);

        if (! $order) {
            return $this->error($this->getLocale() === 'en' ? 'Order not found' : 'الطلب غير موجود', 404);
        }

        if ($order->status !== 'pending') {
            return $this->error($this->getLocale() === 'en' ? 'Cannot cancel order in this status' : 'لا يمكن إلغاء الطلب في هذه المرحلة', 400);
        }

        DB::transaction(function () use ($order, $user) {
            $order->update(['status' => 'cancelled']);

            if ($order->payment_status === 'paid') {
                $before = $user->wallet_balance;
                $user->increment('wallet_balance', $order->total_amount);

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'refund',
                    'amount' => $order->total_amount,
                    'balance_before' => $before,
                    'balance_after' => $user->wallet_balance,
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'description' => [
                        'ar' => "استرجاع مبلغ الطلب الملغي {$order->order_number}",
                        'en' => "Refund for cancelled order {$order->order_number}",
                    ],
                ]);

                $order->update(['payment_status' => 'refunded']);
            }
        });

        $message = $this->getLocale() === 'en'
            ? 'Order cancelled and amount refunded to your wallet'
            : 'تم إلغاء الطلب واسترجاع المبلغ إلى محفظتك بنجاح';

        return $this->success(null, $message);
    }

    /**
     * Download Order Attached File(s)
     */
    public function downloadOrderFile(Request $request, int $id)
    {
        $user = $request->user();
        $order = Order::with('items')->where('user_id', $user->id)->find($id);

        if (! $order) {
            return $this->error($this->getLocale() === 'en' ? 'Order not found' : 'الطلب غير موجود', 404);
        }

        $items = $order->items;
        if ($items->isEmpty()) {
            return $this->error($this->getLocale() === 'en' ? 'No attached files found' : 'لا توجد ملفات مرفقة لهذا الطلب', 404);
        }

        if ($items->count() === 1) {
            $item = $items->first();
            if (! $item->file_path || ! Storage::disk('public')->exists($item->file_path)) {
                return $this->error($this->getLocale() === 'en' ? 'File not found on server' : 'الملف غير موجود على السيرفر', 404);
            }

            return Storage::disk('public')->download($item->file_path, $item->original_file_name ?: basename($item->file_path));
        }

        $zipFileName = "order_{$order->order_number}_files.zip";
        $tempPath = tempnam(sys_get_temp_dir(), 'order_zip_');

        $zip = new ZipArchive;
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return $this->error($this->getLocale() === 'en' ? 'Failed to create zip' : 'فشل إنشاء الملف المضغوط', 500);
        }

        $added = 0;
        foreach ($items as $idx => $item) {
            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                $zip->addFile(Storage::disk('public')->path($item->file_path), ($idx + 1).'_'.($item->original_file_name ?: basename($item->file_path)));
                $added++;
            }
        }
        $zip->close();

        if ($added === 0) {
            @unlink($tempPath);

            return $this->error($this->getLocale() === 'en' ? 'Files not found on server' : 'الملفات غير موجودة على السيرفر', 404);
        }

        return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Download Specific Item File
     */
    public function downloadItemFile(Request $request, int $orderId, int $itemId)
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)->find($orderId);

        if (! $order) {
            return $this->error($this->getLocale() === 'en' ? 'Order not found' : 'الطلب غير موجود', 404);
        }

        $item = $order->items()->find($itemId);
        if (! $item || ! $item->file_path || ! Storage::disk('public')->exists($item->file_path)) {
            return $this->error($this->getLocale() === 'en' ? 'File not found on server' : 'الملف غير موجود على السيرفر', 404);
        }

        return Storage::disk('public')->download($item->file_path, $item->original_file_name ?: basename($item->file_path));
    }
}
