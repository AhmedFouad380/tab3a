<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\VerifyKioskRequest;
use App\Models\KioskHealthCheck;
use App\Models\KioskMachine;
use App\Models\KioskSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KioskController extends BaseApiController
{
    /**
     * Verify Kiosk Machine Status & Supplies via QR Code or NFC Tag
     */
    public function verifyMachine(VerifyKioskRequest $request): JsonResponse
    {
        $query = KioskMachine::query();

        if ($request->filled('machine_code')) {
            $query->where('machine_code', $request->machine_code);
        } elseif ($request->filled('qr_token')) {
            $query->where(function ($q) use ($request) {
                $q->where('qr_token', $request->qr_token)
                    ->orWhere('machine_code', $request->qr_token);
            });
        } elseif ($request->filled('nfc_tag_id')) {
            $query->where('nfc_tag_id', $request->nfc_tag_id);
        } elseif ($request->filled('session_token')) {
            $sess = KioskSession::where('session_token', $request->session_token)->first();
            if ($sess) {
                $query->where('id', $sess->kiosk_machine_id);
            }
        } else {
            return $this->error($this->getLocale() === 'en' ? 'Machine identifier (QR, NFC, machine code, or session token) is required' : 'معرف الماكينة مطلوب (كود، QR، NFC، أو رمز الجلسة)', 422);
        }

        $kiosk = $query->first();

        if (! $kiosk) {
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
        $paperPassed = ($availableSheets >= $sheetsNeeded) && ! $kiosk->is_paper_empty;

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
        if ($colorMode === 'color' && ! $kiosk->supports_color) {
            $settingsPassed = false;
        }
        if ($sideMode === 'double_sided' && ! $kiosk->supports_duplex) {
            $settingsPassed = false;
        }

        $isReadyToPrint = $connectionPassed && $paperPassed && $tonerPassed && $settingsPassed;

        // Build User-Friendly Error Messages
        $failedStep = null;
        $errorMessage = null;

        if (! $connectionPassed) {
            $failedStep = 'connection';
            $errorMessage = [
                'ar' => 'تعذر الاتصال بالماكينة حالياً، يرجى المحاولة مرة أخرى أو اختيار ماكينة أخرى.',
                'en' => 'Unable to connect to the machine currently. Please try another kiosk.',
            ];
        } elseif (! $paperPassed) {
            $failedStep = 'paper';
            $errorMessage = [
                'ar' => "لا تحتوي الماكينة على ورق {$paperSize} كافٍ حالياً ({$availableSheets} ورقة متاحة والمطلوب {$sheetsNeeded}). يمكنك تجربة ماكينة أخرى أو إعادة الفحص بعد التزويد بالورق.",
                'en' => "The kiosk does not have enough {$paperSize} paper currently. Please select another machine.",
            ];
        } elseif (! $tonerPassed) {
            $failedStep = 'toner';
            $errorMessage = [
                'ar' => 'مستوى الحبر غير كافٍ لإتمام الطباعة بجودة مناسبة. يرجى تجربة ماكينة أخرى.',
                'en' => 'Toner level is too low to complete this print job. Please try another kiosk.',
            ];
        } elseif (! $settingsPassed) {
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

        // Sync with Kiosk Screen: set session to 'connected'
        $session = null;
        if ($request->filled('session_token')) {
            $session = KioskSession::where('session_token', $request->session_token)->first();
        }

        if (! $session) {
            $session = KioskSession::where('kiosk_machine_id', $kiosk->id)
                ->whereIn('status', ['waiting_scan', 'connected'])
                ->latest()
                ->first();
        }

        if (! $session) {
            $session = KioskSession::create([
                'kiosk_machine_id' => $kiosk->id,
                'session_token' => 'SESS-'.strtoupper(bin2hex(random_bytes(4))),
                'status' => 'connected',
                'connected_at' => now(),
                'expires_at' => now()->addMinutes(10),
            ]);
        } else {
            $session->update([
                'status' => 'connected',
                'connected_at' => now(),
            ]);
        }

        return $this->success([
            'session_token' => $session->session_token,
            'kiosk' => [
                'id' => $kiosk->id,
                'machine_code' => $kiosk->machine_code,
                'name' => $this->localize($kiosk->name),
                'location' => $this->localize($kiosk->location_description),
                'status' => $kiosk->status,
                'paper_tray_a4_sheets' => $kiosk->paper_tray_a4_sheets,
            ],
            'checks' => [
                'connection_passed' => $connectionPassed,
                'paper_passed' => $paperPassed,
                'toner_passed' => $tonerPassed,
                'settings_passed' => $settingsPassed,
                'is_ready_to_print' => $isReadyToPrint,
            ],
            'constraints' => [
                'max_file_size_mb' => $kiosk->getEffectiveMaxFileSizeMb(),
                'min_order_price' => $kiosk->getEffectiveMinOrderPrice(),
                'max_sheets_per_job' => $kiosk->getEffectiveMaxSheets(),
                'currency' => 'SAR',
            ],
            'failed_step' => $failedStep,
            'error_details' => $errorMessage ? $this->localize($errorMessage) : null,
        ]);
    }

    /**
     * Connect mobile app to kiosk screen by scanning QR code or NFC tag
     */
    public function connect(Request $request): JsonResponse
    {
        $query = KioskMachine::query();

        if ($request->filled('session_token')) {
            $sess = KioskSession::where('session_token', $request->session_token)->first();
            if ($sess) {
                $query->where('id', $sess->kiosk_machine_id);
            }
        } elseif ($request->filled('qr_token')) {
            $query->where(function ($q) use ($request) {
                $q->where('qr_token', $request->qr_token)
                    ->orWhere('machine_code', $request->qr_token);
            });
        } elseif ($request->filled('machine_code')) {
            $query->where('machine_code', $request->machine_code);
        } elseif ($request->filled('nfc_tag_id')) {
            $query->where('nfc_tag_id', $request->nfc_tag_id);
        } else {
            return $this->error($this->getLocale() === 'en' ? 'Machine identifier is required' : 'معرف الماكينة مطلوب (كود، QR، أو NFC)', 422);
        }

        $kiosk = $query->first();

        if (! $kiosk) {
            return $this->error($this->getLocale() === 'en' ? 'Kiosk machine not found' : 'لم يتم العثور على ماكينة الطباعة', 404);
        }

        // Find or create active session and transition to 'connected'
        $session = null;
        if ($request->filled('session_token')) {
            $session = KioskSession::where('session_token', $request->session_token)->first();
        }

        if (! $session) {
            $session = KioskSession::where('kiosk_machine_id', $kiosk->id)
                ->whereIn('status', ['waiting_scan', 'connected'])
                ->latest()
                ->first();
        }

        if (! $session) {
            $session = KioskSession::create([
                'kiosk_machine_id' => $kiosk->id,
                'session_token' => 'SESS-'.strtoupper(bin2hex(random_bytes(4))),
                'status' => 'connected',
                'connected_at' => now(),
                'expires_at' => now()->addSeconds(90),
            ]);
        } else {
            $session->update([
                'status' => 'connected',
                'connected_at' => now(),
                'expires_at' => now()->addSeconds(90),
            ]);
        }

        return $this->success([
            'session_token' => $session->session_token,
            'kiosk' => [
                'id' => $kiosk->id,
                'machine_code' => $kiosk->machine_code,
                'name' => $this->localize($kiosk->name),
                'location' => $this->localize($kiosk->location_description),
                'status' => $kiosk->status,
                'supports_color' => $kiosk->supports_color,
                'supports_duplex' => $kiosk->supports_duplex,
                'paper_tray_a4_sheets' => $kiosk->paper_tray_a4_sheets,
                'paper_tray_a3_sheets' => $kiosk->paper_tray_a3_sheets,
                'is_ready_to_print' => in_array($kiosk->status, ['online', 'busy']) && ! $kiosk->is_paper_empty,
            ],
            'constraints' => [
                'max_file_size_mb' => $kiosk->getEffectiveMaxFileSizeMb(),
                'min_order_price' => $kiosk->getEffectiveMinOrderPrice(),
                'max_sheets_per_job' => $kiosk->getEffectiveMaxSheets(),
                'currency' => 'SAR',
            ],
        ], $this->getLocale() === 'en' ? 'Connected to kiosk successfully' : 'تم الاتصال بالماكينة وتحديث الشاشة بنجاح');
    }

    /**
     * Update Kiosk Session Status from Mobile (e.g. awaiting_payment, cancelled)
     */
    public function updateSessionStatus(Request $request): JsonResponse
    {
        $request->validate([
            'session_token' => 'required|string',
            'status' => 'required|in:connected,file_uploaded,awaiting_payment,printing,completed,cancelled',
        ]);

        $session = KioskSession::where('session_token', $request->session_token)->first();
        if (! $session) {
            return $this->error($this->getLocale() === 'en' ? 'Session not found' : 'جلسة الكشك غير موجودة أو منتهية', 404);
        }

        $session->update([
            'status' => $request->status,
            'expires_at' => now()->addSeconds(90),
        ]);

        return $this->success([
            'session_token' => $session->session_token,
            'status' => $session->status,
        ], $this->getLocale() === 'en' ? 'Kiosk screen status updated' : 'تم تحديث حالة شاشة الكشك بنجاح');
    }

    /**
     * Extend Kiosk Session (Keep-Alive / Reset Inactivity Timer)
     */
    public function extendSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_token' => 'required|string',
        ]);

        $session = KioskSession::where('session_token', $request->session_token)->first();
        if (! $session) {
            return $this->error($this->getLocale() === 'en' ? 'Session not found or expired' : 'جلسة الكشك غير موجودة أو منتهية', 404);
        }

        $session->update([
            'expires_at' => now()->addSeconds(90),
            'updated_at' => now(),
        ]);

        return $this->success([
            'session_token' => $session->session_token,
            'status' => $session->status,
            'expires_at' => $session->expires_at?->toIso8601String(),
        ], $this->getLocale() === 'en' ? 'Session extended successfully' : 'تم تمديد وقت الجلسة بنجاح');
    }
}
