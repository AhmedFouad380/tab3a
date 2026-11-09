<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\FileUploadRequest;
use App\Http\Requests\Api\V1\PriceCalculationRequest;
use App\Models\FinishingOption;
use App\Models\KioskMachine;
use App\Models\KioskSession;
use App\Models\PricingRule;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Smalot\PdfParser\Parser;

class FileController extends BaseApiController
{
    /**
     * Upload Document & Inspect Page Count Automatically
     */
    public function uploadAndInspect(FileUploadRequest $request): JsonResponse
    {
        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $fileSizeBytes = $uploadedFile->getSize();
        $fileSizeMb = round($fileSizeBytes / (1024 * 1024), 1);

        // Store file
        $path = $uploadedFile->store('uploads', 'public');
        $fullPath = storage_path('app/public/'.$path);

        $detectedPageCount = 1;

        // Auto-detect PDF pages count
        if ($extension === 'pdf') {
            try {
                $parser = new Parser;
                $pdf = $parser->parseFile($fullPath);
                $pages = $pdf->getPages();
                $detectedPageCount = count($pages) ?: 1;
            } catch (\Throwable $e) {
                $fp = @fopen($fullPath, 'r');
                $count = 0;
                if ($fp) {
                    while (! feof($fp)) {
                        $line = fgets($fp, 255);
                        if (preg_match('/\/Count\s+(\d+)/', $line, $matches)) {
                            $count = (int) $matches[1];
                            break;
                        }
                    }
                    fclose($fp);
                }
                $detectedPageCount = $count > 0 ? $count : 1;
            }
        }

        // Sync with Kiosk Screen if kiosk session or machine ID is provided
        $kioskSession = null;
        $kiosk = null;

        if ($request->filled('session_token')) {
            $kioskSession = KioskSession::where('session_token', $request->session_token)->first();
            $kiosk = $kioskSession?->kiosk;
        }

        if (! $kiosk && $request->filled('kiosk_machine_id')) {
            $kiosk = KioskMachine::find($request->kiosk_machine_id);
            if (! $kioskSession && $kiosk) {
                $kioskSession = KioskSession::where('kiosk_machine_id', $kiosk->id)
                    ->whereIn('status', ['waiting_scan', 'connected', 'file_uploaded', 'awaiting_payment'])
                    ->latest()
                    ->first();
            }
        }

        $copies = max(1, (int) ($request->copies ?? 1));
        $colorMode = $request->color_mode ?? 'black_and_white';
        $sideMode = $request->side_mode ?? 'single_sided';
        $sheetsPerCopy = $sideMode === 'double_sided' ? (int) ceil($detectedPageCount / 2) : $detectedPageCount;
        $totalSheets = $sheetsPerCopy * $copies;

        // If this upload is for a Kiosk (Instant Self-Printing), strictly validate constraints
        if ($kiosk) {
            // 1. Max sheets allowed for this kiosk machine
            $maxAllowedSheets = $kiosk->getEffectiveMaxSheets();
            if ($totalSheets > $maxAllowedSheets) {
                @unlink($fullPath);

                return $this->error(
                    $this->getLocale() === 'en'
                        ? "The uploaded file requires {$totalSheets} sheets ({$detectedPageCount} pages x {$copies} copies), which exceeds the maximum allowed ({$maxAllowedSheets} sheets) for instant printing on this kiosk."
                        : "يتجاوز عدد الأوراق المطلوب ({$totalSheets} ورقة) الحد الأقصى المسموح به للطباعة الفورية على هذه الماكينة ({$maxAllowedSheets} ورقة). فضلاً قلل عدد الصفحات أو النسخ أو استخدم خدمة الطلب المسبق.",
                    422,
                    [
                        'detected_page_count' => $detectedPageCount,
                        'copies' => $copies,
                        'total_sheets_needed' => $totalSheets,
                        'max_allowed_sheets' => $maxAllowedSheets,
                    ]
                );
            }

            // 2. Paper availability in tray
            $paperSize = $request->input('paper_size', 'A4');
            $availableInTray = $paperSize === 'A3' ? $kiosk->paper_tray_a3_sheets : $kiosk->paper_tray_a4_sheets;
            if ($totalSheets > $availableInTray) {
                @unlink($fullPath);

                return $this->error(
                    $this->getLocale() === 'en'
                        ? "The machine does not have enough {$paperSize} paper ({$availableInTray} sheets available, required: {$totalSheets})."
                        : "الماكينة لا تحتوي على ورق كافٍ حالياً في درج {$paperSize} ({$availableInTray} ورقة متاحة، والمطلوب {$totalSheets} ورقة).",
                    422,
                    [
                        'available_in_tray' => $availableInTray,
                        'total_sheets_needed' => $totalSheets,
                    ]
                );
            }

            // 3. Color support & toner checks
            if ($colorMode === 'color') {
                if (! $kiosk->supports_color) {
                    @unlink($fullPath);

                    return $this->error(
                        $this->getLocale() === 'en'
                            ? 'This kiosk machine only supports Black & White printing. Please switch to Black & White.'
                            : 'ماكينة الطباعة المحددة تدعم الطباعة بالأبيض والأسود فقط. يرجى اختيار الطباعة بالأبيض والأسود.',
                        422
                    );
                }

                if ($kiosk->cyan_toner_level < 5 || $kiosk->magenta_toner_level < 5 || $kiosk->yellow_toner_level < 5) {
                    @unlink($fullPath);

                    return $this->error(
                        $this->getLocale() === 'en'
                            ? 'Color toner levels are too low on this machine. Please switch to Black & White or select another kiosk.'
                            : 'مستوى الحبر الملون في الماكينة غير كافٍ للطباعة الملونة. يمكنك اختيار الطباعة بالأبيض والأسود لإتمام العملية.',
                        422
                    );
                }
            } else {
                if ($kiosk->black_toner_level < 5) {
                    @unlink($fullPath);

                    return $this->error(
                        $this->getLocale() === 'en'
                            ? 'Black toner level is too low to print. Please select another kiosk.'
                            : 'مستوى الحبر الأسود غير كافٍ لإتمام الطباعة. يرجى تجربة ماكينة أخرى.',
                        422
                    );
                }
            }

            // 4. Duplex support
            if ($sideMode === 'double_sided' && ! $kiosk->supports_duplex) {
                @unlink($fullPath);

                return $this->error(
                    $this->getLocale() === 'en'
                        ? 'This kiosk machine does not support double-sided printing. Please choose single-sided.'
                        : 'ماكينة الطباعة المحددة لا تدعم الطباعة على الوجهين. يرجى اختيار الطباعة على وجه واحد.',
                    422
                );
            }
        }

        if ($kioskSession) {
            $rule = PricingRule::where('paper_size', 'A4')
                ->where('color_mode', $colorMode)
                ->where('side_mode', $sideMode)
                ->where('is_active', true)
                ->first();

            $unitPrice = $rule ? (float) $rule->price_per_page : ($colorMode === 'color' ? 1.50 : 0.50);
            $subtotal = ($unitPrice * $detectedPageCount) * $copies;

            $minPrice = $kiosk ? $kiosk->getEffectiveMinOrderPrice() : $kioskSession->kiosk->getEffectiveMinOrderPrice();
            if ($subtotal < $minPrice) {
                $subtotal = $minPrice;
            }
            $totalPrice = round($subtotal * 1.15, 2);

            $kioskSession->update([
                'status' => 'file_uploaded',
                'file_name' => $originalName,
                'file_path' => $path,
                'file_size_bytes' => $fileSizeBytes,
                'detected_page_count' => $detectedPageCount,
                'copies_count' => $copies,
                'total_sheets' => $totalSheets,
                'color_mode' => $colorMode,
                'side_mode' => $sideMode,
                'total_price' => $totalPrice,
                'expires_at' => now()->addSeconds(90),
            ]);
        }

        $message = $this->getLocale() === 'en'
            ? 'File uploaded and inspected successfully'
            : 'تم رفع الملف وقراءة عدد الصفحات بنجاح';

        return $this->success([
            'file_path' => $path,
            'file_url' => asset('storage/'.$path),
            'original_file_name' => $originalName,
            'file_extension' => $extension,
            'file_size_bytes' => $fileSizeBytes,
            'file_size_formatted' => $fileSizeMb.' MB',
            'detected_page_count' => $detectedPageCount,
            'kiosk_session_token' => $kioskSession?->session_token,
            'kiosk_screen_updated' => $kioskSession !== null,
        ], $message);
    }

    /**
     * Calculate Printing Price Breakdown
     */
    public function calculatePrice(PriceCalculationRequest $request): JsonResponse
    {
        $pagesToPrint = (int) $request->detected_page_count;
        $copies = (int) $request->copies_count;
        $sideMode = $request->side_mode;
        $paperSize = $request->paper_size;
        $colorMode = $request->color_mode;

        // Calculate pages if custom range provided
        if ($request->filled('page_range_selection') && $request->page_range_selection !== 'all') {
            $range = $request->page_range_selection;
            if (str_contains($range, '-')) {
                $parts = explode('-', $range);
                $start = (int) ($parts[0] ?? 1);
                $end = (int) ($parts[1] ?? $pagesToPrint);
                $pagesToPrint = max(1, ($end - $start + 1));
            }
        }

        // Calculate sheets needed
        $sheetsPerCopy = $sideMode === 'double_sided' ? (int) ceil($pagesToPrint / 2) : $pagesToPrint;
        $totalSheets = $sheetsPerCopy * $copies;

        // Check if calculating for kiosk / self-printing or pre-order
        $kioskSession = null;
        $kiosk = null;
        if ($request->filled('session_token')) {
            $kioskSession = KioskSession::where('session_token', $request->session_token)->first();
            $kiosk = $kioskSession?->kiosk;
        } elseif ($request->filled('kiosk_machine_id')) {
            $kioskSession = KioskSession::where('kiosk_machine_id', $request->kiosk_machine_id)
                ->whereIn('status', ['waiting_scan', 'connected', 'file_uploaded', 'awaiting_payment'])
                ->latest()
                ->first();
            $kiosk = $kioskSession?->kiosk ?? KioskMachine::find($request->kiosk_machine_id);
        }

        if ($kiosk) {
            $maxAllowedSheets = $kiosk->getEffectiveMaxSheets();
            if ($totalSheets > $maxAllowedSheets) {
                return $this->error(
                    $this->getLocale() === 'en'
                        ? "The order requires {$totalSheets} sheets, which exceeds the maximum allowed ({$maxAllowedSheets} sheets) for instant printing on this kiosk."
                        : "يتجاوز عدد الأوراق المطلوب ({$totalSheets} ورقة) الحد الأقصى المسموح به للطباعة الفورية على هذه الماكينة ({$maxAllowedSheets} ورقة). فضلاً قلل عدد الصفحات أو النسخ أو استخدم خدمة الطلب المسبق.",
                    422,
                    [
                        'pages_to_print' => $pagesToPrint,
                        'copies' => $copies,
                        'total_sheets_needed' => $totalSheets,
                        'max_allowed_sheets' => $maxAllowedSheets,
                    ]
                );
            }

            $availableInTray = $paperSize === 'A3' ? $kiosk->paper_tray_a3_sheets : $kiosk->paper_tray_a4_sheets;
            if ($totalSheets > $availableInTray) {
                return $this->error(
                    $this->getLocale() === 'en'
                        ? "The machine does not have enough {$paperSize} paper ({$availableInTray} sheets available, required: {$totalSheets})."
                        : "الماكينة لا تحتوي على ورق كافٍ حالياً في درج {$paperSize} ({$availableInTray} ورقة متاحة، والمطلوب {$totalSheets} ورقة).",
                    422
                );
            }
        }

        // Find pricing rule matching service_type
        $isKiosk = (bool) $kiosk;
        $ruleQuery = PricingRule::where('paper_size', $paperSize)
            ->where('color_mode', $colorMode)
            ->where('side_mode', $sideMode)
            ->where('is_active', true);

        if ($isKiosk) {
            $ruleQuery->whereIn('service_type', ['self_printing', 'all'])
                ->orderByRaw("CASE WHEN service_type = 'self_printing' THEN 1 ELSE 2 END");
        } else {
            $ruleQuery->whereIn('service_type', ['pre_order', 'all'])
                ->orderByRaw("CASE WHEN service_type = 'pre_order' THEN 1 ELSE 2 END");
        }
        $rule = $ruleQuery->first();

        $unitPricePerPage = $rule ? (float) $rule->price_per_page : ($colorMode === 'color' ? 1.50 : 0.50);
        $printSubtotal = ($unitPricePerPage * $pagesToPrint) * $copies;

        // Finishing option
        $finishingPrice = 0.00;
        if ($request->filled('finishing_option_id')) {
            $finishing = FinishingOption::find($request->finishing_option_id);
            if ($finishing) {
                $finishingPrice = ((float) $finishing->base_price) * $copies;
            }
        }

        $subtotal = $printSubtotal + $finishingPrice;

        // Apply minimum order price for kiosk / self-printing
        if ($kiosk) {
            $minPrice = $kiosk->getEffectiveMinOrderPrice();
            if ($subtotal < $minPrice) {
                $subtotal = $minPrice;
            }
        }

        // Tax (VAT from system settings or 15%)
        $taxPercentage = (float) (SystemSetting::getSetting('tax_rate_percentage', 'ar', 15));
        $taxRate = $taxPercentage > 0 ? ($taxPercentage / 100) : 0.15;
        $taxAmount = round($subtotal * $taxRate, 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        if ($kioskSession) {
            $newStatus = $request->boolean('is_checkout_step') ? 'awaiting_payment' : 'file_uploaded';
            $kioskSession->update([
                'status' => $newStatus,
                'detected_page_count' => $pagesToPrint,
                'copies_count' => $copies,
                'total_sheets' => $totalSheets,
                'paper_size' => $paperSize,
                'color_mode' => $colorMode,
                'side_mode' => $sideMode,
                'total_price' => $totalAmount,
                'expires_at' => now()->addSeconds(90),
            ]);
        }

        return $this->success([
            'pages_to_print_count' => $pagesToPrint,
            'copies_count' => $copies,
            'sheets_per_copy' => $sheetsPerCopy,
            'total_sheets_needed' => $totalSheets,
            'unit_price_per_page' => $unitPricePerPage,
            'print_subtotal' => round($printSubtotal, 2),
            'finishing_price' => round($finishingPrice, 2),
            'subtotal' => round($subtotal, 2),
            'tax_rate_percentage' => 15,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'currency' => 'SAR',
            'is_teacher' => $request->boolean('is_teacher'),
            'capacity' => $kiosk ? [
                'paper_size' => $paperSize,
                'tray_sheets_available' => $paperSize === 'A3' ? (int) $kiosk->paper_tray_a3_sheets : (int) $kiosk->paper_tray_a4_sheets,
                'supports_color' => (bool) $kiosk->supports_color,
                'supports_duplex' => (bool) $kiosk->supports_duplex,
                'is_bw_ready' => $kiosk->black_toner_level >= 5,
                'is_color_ready' => $kiosk->supports_color
                    && $kiosk->cyan_toner_level >= 5
                    && $kiosk->magenta_toner_level >= 5
                    && $kiosk->yellow_toner_level >= 5,
            ] : null,
            'kiosk_session_token' => $kioskSession?->session_token,
            'kiosk_screen_status' => $kioskSession?->status,
        ]);
    }
}
