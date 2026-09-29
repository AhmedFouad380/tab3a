<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\FileUploadRequest;
use App\Http\Requests\Api\V1\PriceCalculationRequest;
use App\Models\FinishingOption;
use App\Models\PricingRule;
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
        $fullPath = storage_path('app/public/' . $path);

        $detectedPageCount = 1;

        // Auto-detect PDF pages count
        if ($extension === 'pdf') {
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($fullPath);
                $pages = $pdf->getPages();
                $detectedPageCount = count($pages) ?: 1;
            } catch (\Throwable $e) {
                $fp = @fopen($fullPath, 'r');
                $count = 0;
                if ($fp) {
                    while (!feof($fp)) {
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

        $message = $this->getLocale() === 'en'
            ? 'File uploaded and inspected successfully'
            : 'تم رفع الملف وقراءة عدد الصفحات بنجاح';

        return $this->success([
            'file_path' => $path,
            'file_url' => asset('storage/' . $path),
            'original_file_name' => $originalName,
            'file_extension' => $extension,
            'file_size_bytes' => $fileSizeBytes,
            'file_size_formatted' => $fileSizeMb . ' MB',
            'detected_page_count' => $detectedPageCount,
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

        // Find pricing rule
        $rule = PricingRule::where('paper_size', $paperSize)
            ->where('color_mode', $colorMode)
            ->where('side_mode', $sideMode)
            ->where('is_active', true)
            ->first();

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

        // Tax (VAT 15%)
        $taxRate = 0.15;
        $taxAmount = round($subtotal * $taxRate, 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

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
        ]);
    }
}
