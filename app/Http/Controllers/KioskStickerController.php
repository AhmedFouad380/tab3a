<?php

namespace App\Http\Controllers;

use App\Models\KioskMachine;
use App\Models\SystemSetting;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KioskStickerController extends Controller
{
    /**
     * Download the Machine QR Code as a high-resolution SVG file
     */
    public function downloadQr(KioskMachine $kiosk)
    {
        $connectUrl = request()->getSchemeAndHttpHost().'/kiosk-connect/'.$kiosk->qr_token;

        $svg = QrCode::size(600)
            ->errorCorrection('H')
            ->margin(2)
            ->generate($connectUrl);

        $fileName = 'QR_'.$kiosk->machine_code.'_'.($kiosk->branch?->name['en'] ?? 'kiosk').'.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Show a printable, beautifully formatted Sticker / Poster for the physical printer
     */
    public function printSticker(KioskMachine $kiosk)
    {
        $connectUrl = request()->getSchemeAndHttpHost().'/kiosk-connect/'.$kiosk->qr_token;

        $qrSvg = QrCode::size(340)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($connectUrl);

        $appName = SystemSetting::where('key', 'app_name')->first()?->value['ar'] ?? 'منصة طباعة';
        $supportPhone = SystemSetting::where('key', 'support_phone')->first()?->value['ar'] ?? '+966500000000';

        return view('kiosk.sticker', compact('kiosk', 'qrSvg', 'connectUrl', 'appName', 'supportPhone'));
    }
}
