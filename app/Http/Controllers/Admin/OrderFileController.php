<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class OrderFileController extends Controller
{
    /**
     * Download all files for an order, or the single file directly.
     */
    public function downloadOrder(Request $request, Order $order): BinaryFileResponse|StreamedResponse
    {
        $items = $order->items;

        if ($items->isEmpty()) {
            abort(404, 'لا توجد ملفات مرفقة لهذا الطلب.');
        }

        // If specific item requested via query param
        if ($request->has('item_id')) {
            $specificItem = $items->firstWhere('id', (int) $request->query('item_id'));
            if ($specificItem) {
                return $this->downloadItem($specificItem);
            }
        }

        // If single item, download it directly with original name
        if ($items->count() === 1) {
            return $this->downloadItem($items->first());
        }

        // For multiple items, bundle into a ZIP archive
        $zipFileName = "طلب_{$order->order_number}_الملفات.zip";
        $tempPath = tempnam(sys_get_temp_dir(), 'order_zip_');

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'تعذر إنشاء ملف الأرشيف المضغوط.');
        }

        $filesAdded = 0;
        foreach ($items as $index => $item) {
            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                $absolutePath = Storage::disk('public')->path($item->file_path);
                $originalName = $item->original_file_name ?: basename($item->file_path);
                $zipEntryName = ($index + 1) . '_' . $originalName;
                $zip->addFile($absolutePath, $zipEntryName);
                $filesAdded++;
            }
        }

        $zip->close();

        if ($filesAdded === 0) {
            @unlink($tempPath);
            abort(404, 'الملفات المرفقة غير موجودة على الخادم.');
        }

        return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Download a specific order item file.
     */
    public function downloadItem(OrderItem $orderItem): StreamedResponse
    {
        if (!$orderItem->file_path || !Storage::disk('public')->exists($orderItem->file_path)) {
            abort(404, 'الملف المرفق غير موجود على الخادم.');
        }

        $fileName = $orderItem->original_file_name ?: basename($orderItem->file_path);

        return Storage::disk('public')->download($orderItem->file_path, $fileName);
    }

    /**
     * Preview / inline view an order item file (e.g. PDF).
     */
    public function previewItem(OrderItem $orderItem): BinaryFileResponse
    {
        if (!$orderItem->file_path || !Storage::disk('public')->exists($orderItem->file_path)) {
            abort(404, 'الملف المرفق غير موجود على الخادم.');
        }

        $fullPath = Storage::disk('public')->path($orderItem->file_path);
        $mimeType = Storage::disk('public')->mimeType($orderItem->file_path) ?: 'application/pdf';
        $fileName = $orderItem->original_file_name ?: basename($orderItem->file_path);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . rawurlencode($fileName) . '"',
        ]);
    }
}
