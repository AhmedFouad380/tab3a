<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\VerifyKioskRequest;
use App\Models\KioskHealthCheck;
use App\Models\KioskMachine;
use Illuminate\Http\JsonResponse;

class KioskController extends BaseApiController
{
    /**
     * Verify Kiosk Machine Status & Supplies via QR Code or NFC Tag
     */
    public function verifyMachine(VerifyKioskRequest $request): JsonResponse
    {
        $query = KioskMachine::query();

        if ($request->filled('qr_token')) {
            $query->where('qr_token', $request->qr_token);
        } elseif ($request->filled('nfc_tag_id')) {
            $query->where('nfc_tag_id', $request->nfc_tag_id);
        } elseif ($request->filled('machine_code')) {
            $query->where('machine_code', $request->machine_code);
        } else {
            return $this->error($this->getLocale() === 'en' ? 'QR Code or NFC ID is required' : 'رمز الاستجابة السريعة QR أو NFC مطلوب', 422);
        }

        $kiosk = $query->first();

        if (!$kiosk) {
            return $this->error($this->getLocale() === 'en' ? 'Kiosk machine not found' : 'لم يتم العثور على ماكينة الطباعة', 404);
        }

        $sheetsNeeded = (int) $request->total_sheets_needed;
        $paperSize = $request->paper_size;
        $colorMode = $request->color_mode;
        $sideMode = $request->side_mode;

        // 1. Check Connection
        $connectionPassed = in_array($kiosk->status, ['online', 'busy']);

        // 2. Check Paper
        $availableSheets = $paperSize === 'A3' ? $kiosk->paper_tray_a3_sheets : $kiosk->paper_tray_a4_sheets;
        $paperPassed = ($availableSheets >= $sheetsNeeded) && !$kiosk->is_paper_empty;

        // 3. Check Toner
        $tonerPassed = true;
        if ($kiosk->black_toner_level < 5) {
            $tonerPassed = false;
        }
        if ($colorMode === 'color') {
            if ($kiosk->cyan_toner_level < 5 || $kiosk->magenta_toner_level < 5 || $kiosk->yellow_toner_level < 5) {
                $tonerPassed = false;
            }
        }

        // 4. Check Settings Support
        $settingsPassed = true;
        if ($colorMode === 'color' && !$kiosk->supports_color) {
            $settingsPassed = false;
        }
        if ($sideMode === 'double_sided' && !$kiosk->supports_duplex) {
            $settingsPassed = false;
        }

        $isReadyToPrint = $connectionPassed && $paperPassed && $tonerPassed && $settingsPassed;

        // Build User-Friendly Error Messages
        $failedStep = null;
        $errorMessage = null;

        if (!$connectionPassed) {
            $failedStep = 'connection';
            $errorMessage = [
                'ar' => 'تعذر الاتصال بالماكينة حالياً، يرجى المحاولة مرة أخرى أو اختيار ماكينة أخرى.',
                'en' => 'Unable to connect to the machine currently. Please try another kiosk.',
            ];
        } elseif (!$paperPassed) {
            $failedStep = 'paper';
            $errorMessage = [
                'ar' => "لا تحتوي الماكينة على ورق {$paperSize} كافٍ حالياً ({$availableSheets} ورقة متاحة والمطلوب {$sheetsNeeded}). يمكنك تجربة ماكينة أخرى أو إعادة الفحص بعد التزويد بالورق.",
                'en' => "The kiosk does not have enough {$paperSize} paper currently. Please select another machine.",
            ];
        } elseif (!$tonerPassed) {
            $failedStep = 'toner';
            $errorMessage = [
                'ar' => 'مستوى الحبر غير كافٍ لإتمام الطباعة بجودة مناسبة. يرجى تجربة ماكينة أخرى.',
                'en' => 'Toner level is too low to complete this print job. Please try another kiosk.',
            ];
        } elseif (!$settingsPassed) {
            $failedStep = 'settings';
            $errorMessage = [
                'ar' => 'الماكينة الحالية لا تدعم بعض خيارات الطباعة المحددة (مثل الألوان أو الطباعة على الوجهين).',
                'en' => 'This machine does not support the selected options (Color / Duplex).',
            ];
        }

        // Save Health Check Log
        KioskHealthCheck::create([
            'kiosk_machine_id' => $kiosk->id,
            'user_id' => $request->user('sanctum')?->id,
            'connection_passed' => $connectionPassed,
            'paper_passed' => $paperPassed,
            'toner_passed' => $tonerPassed,
            'settings_passed' => $settingsPassed,
            'is_ready_to_print' => $isReadyToPrint,
            'failed_step' => $failedStep,
            'error_message' => $errorMessage,
            'telemetry_snapshot' => [
                'a4_sheets' => $kiosk->paper_tray_a4_sheets,
                'a3_sheets' => $kiosk->paper_tray_a3_sheets,
                'black_toner' => $kiosk->black_toner_level,
                'status' => $kiosk->status,
            ],
        ]);

        return $this->success([
            'kiosk' => [
                'id' => $kiosk->id,
                'machine_code' => $kiosk->machine_code,
                'name' => $this->localize($kiosk->name),
                'location' => $this->localize($kiosk->location_description),
                'status' => $kiosk->status,
            ],
            'checks' => [
                'connection_passed' => $connectionPassed,
                'paper_passed' => $paperPassed,
                'toner_passed' => $tonerPassed,
                'settings_passed' => $settingsPassed,
                'is_ready_to_print' => $isReadyToPrint,
            ],
            'failed_step' => $failedStep,
            'error_details' => $errorMessage ? $this->localize($errorMessage) : null,
        ]);
    }
}
