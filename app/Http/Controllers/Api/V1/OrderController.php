<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\CreatePreOrderRequest;
use App\Http\Requests\Api\V1\CreateSelfPrintOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\FinishingOption;
use App\Models\KioskMachine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PricingRule;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseApiController
{
    /**
     * Create & Checkout Instant Self-Printing Order
     */
    public function createSelfPrintOrder(CreateSelfPrintOrderRequest $request): JsonResponse
    {
        $user = $request->user();
        $kiosk = KioskMachine::findOrFail($request->kiosk_machine_id);

        $pagesToPrint = (int) $request->detected_page_count;
        $copies = (int) $request->copies_count;
        $sideMode = $request->side_mode;
        $paperSize = $request->paper_size;
        $colorMode = $request->color_mode;

        if ($request->filled('page_range_selection') && $request->page_range_selection !== 'all') {
            $range = $request->page_range_selection;
            if (str_contains($range, '-')) {
                $parts = explode('-', $range);
                $pagesToPrint = max(1, ((int)$parts[1] - (int)$parts[0] + 1));
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
        $subtotal = ($unitPrice * $pagesToPrint) * $copies;
        $taxAmount = round($subtotal * 0.15, 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        if ($request->payment_method === 'wallet' && $user->wallet_balance < $totalAmount) {
            $errMsg = $this->getLocale() === 'en' ? 'Insufficient wallet balance' : 'رصيد المحفظة غير كافٍ لإتمام عملية الطباعة';
            return $this->error($errMsg, 400);
        }

        return DB::transaction(function () use ($request, $user, $kiosk, $pagesToPrint, $copies, $sideMode, $paperSize, $colorMode, $totalSheets, $unitPrice, $subtotal, $taxAmount, $totalAmount) {
            $orderNumber = 'TAB-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'order_type' => 'self_printing',
                'branch_id' => $kiosk->branch_id,
                'kiosk_machine_id' => $kiosk->id,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'printer_job_id' => 'JOB-' . uniqid(),
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
                'total_sheets_needed' => $totalSheets,
                'unit_price_per_page' => $unitPrice,
                'total_item_price' => $subtotal,
            ]);

            if ($paperSize === 'A3') {
                $kiosk->decrement('paper_tray_a3_sheets', min($kiosk->paper_tray_a3_sheets, $totalSheets));
            } else {
                $kiosk->decrement('paper_tray_a4_sheets', min($kiosk->paper_tray_a4_sheets, $totalSheets));
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
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
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
                $pagesToPrint = max(1, ((int)$parts[1] - (int)$parts[0] + 1));
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
            $orderNumber = 'TAB-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'order_type' => 'pre_order',
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
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
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

        if (!$order) {
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

        if (!$order) {
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
}
