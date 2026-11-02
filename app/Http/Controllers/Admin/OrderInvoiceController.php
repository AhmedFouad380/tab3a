<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderInvoiceController extends Controller
{
    /**
     * Display a beautiful, printable e-invoice for the given order.
     */
    public function show(Request $request, Order $order)
    {
        $order->load(['user', 'branch', 'kiosk.branch', 'items.finishingOption', 'payments']);

        // Merchant details from settings with sensible fallbacks
        $companyName = SystemSetting::getSetting('company_name', 'ar')
            ?: SystemSetting::getSetting('app_name', 'ar', 'قرطاسية منصور');
        $companyNameEn = SystemSetting::getSetting('company_name', 'en')
            ?: SystemSetting::getSetting('app_name', 'en', 'Mansour Stationery');
        $vatNumber = SystemSetting::getSetting('vat_number', 'ar', '300987654300003');
        $crNumber = SystemSetting::getSetting('cr_number', 'ar', '1010876543');
        $supportPhone = SystemSetting::getSetting('support_phone', 'ar', '+966557226090');
        $supportEmail = SystemSetting::getSetting('support_email', 'ar', 'support@taba3a.sa');

        // Build ZATCA Phase 1 compliant TLV Base64 QR Code
        $zatcaQrBase64 = $this->generateZatcaTlvQr(
            sellerName: $companyName,
            vatNumber: $vatNumber,
            timestamp: $order->created_at?->toIso8601ZuluString() ?? now()->toIso8601ZuluString(),
            totalWithVat: number_format((float) $order->total_amount, 2, '.', ''),
            vatTotal: number_format((float) $order->tax_amount, 2, '.', '')
        );

        $qrSvg = (string) QrCode::size(125)
            ->errorCorrection('M')
            ->margin(0)
            ->generate($zatcaQrBase64);

        $settingLogo = SystemSetting::getSetting('app_logo', 'ar', null);
        $companyLogoUrl = null;
        if ($settingLogo) {
            $companyLogoUrl = str_starts_with($settingLogo, 'http')
                ? $settingLogo
                : (file_exists(public_path($settingLogo)) ? asset($settingLogo) : asset('storage/'.$settingLogo));
        } elseif (file_exists(public_path('logo.jpeg'))) {
            $companyLogoUrl = asset('logo.jpeg');
        }

        return view('admin.orders.invoice', compact(
            'order',
            'companyName',
            'companyNameEn',
            'companyLogoUrl',
            'vatNumber',
            'crNumber',
            'supportPhone',
            'supportEmail',
            'qrSvg'
        ));
    }

    /**
     * Generate standard ZATCA TLV Base64 string for electronic invoices.
     */
    protected function generateZatcaTlvQr(
        string $sellerName,
        string $vatNumber,
        string $timestamp,
        string $totalWithVat,
        string $vatTotal
    ): string {
        $tags = [
            1 => $sellerName,
            2 => $vatNumber,
            3 => $timestamp,
            4 => $totalWithVat,
            5 => $vatTotal,
        ];

        $tlv = '';
        foreach ($tags as $tagNumber => $value) {
            $value = (string) $value;
            $length = strlen($value);
            $tlv .= pack('C', $tagNumber).pack('C', $length).$value;
        }

        return base64_encode($tlv);
    }
}
