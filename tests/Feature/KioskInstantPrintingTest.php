<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\KioskMachine;
use App\Models\KioskSession;
use App\Models\PricingRule;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KioskInstantPrintingTest extends TestCase
{
    use RefreshDatabase;

    private function setupKiosk(): KioskMachine
    {
        $branch = Branch::create([
            'name' => ['ar' => 'فرع النخيل', 'en' => 'Nakheel Branch'],
            'address' => ['ar' => 'طريق الملك فهد', 'en' => 'King Fahd Rd'],
            'city' => 'الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
        ]);

        SystemSetting::updateOrCreate(
            ['key' => 'instant_print_max_file_size'],
            [
                'group' => 'print_settings',
                'type' => 'number',
                'display_name' => ['ar' => 'الحد الأقصى لحجم الملف', 'en' => 'Max File Size'],
                'value' => ['ar' => '30', 'en' => '30'],
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'instant_print_min_order_price'],
            [
                'group' => 'pricing',
                'type' => 'number',
                'display_name' => ['ar' => 'الحد الأدنى لقيمة العملية', 'en' => 'Min Order Price'],
                'value' => ['ar' => '2.00', 'en' => '2.00'],
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'instant_print_max_sheets'],
            [
                'group' => 'print_settings',
                'type' => 'number',
                'display_name' => ['ar' => 'أقصى أوراق', 'en' => 'Max Sheets'],
                'value' => ['ar' => '50', 'en' => '50'],
            ]
        );

        PricingRule::create([
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'price_per_page' => 0.50,
            'is_active' => true,
        ]);

        return KioskMachine::create([
            'branch_id' => $branch->id,
            'machine_code' => 'KSK-TEST-01',
            'qr_token' => 'QR-TEST-001',
            'name' => ['ar' => 'كشك الاختبار', 'en' => 'Test Kiosk'],
            'status' => 'online',
            'paper_tray_a4_sheets' => 200,
            'paper_tray_a3_sheets' => 50,
            'black_toner_level' => 100,
            'supports_color' => true,
            'supports_duplex' => true,
        ]);
    }

    public function test_kiosk_qr_download_and_sticker(): void
    {
        $kiosk = $this->setupKiosk();

        // 1. Download QR SVG
        $qrResponse = $this->get(route('kiosk.qr.download', $kiosk->id));
        $qrResponse->assertOk();
        $qrResponse->assertHeader('Content-Type', 'image/svg+xml');

        // 2. View Sticker Page
        $stickerResponse = $this->get(route('kiosk.sticker', $kiosk->id));
        $stickerResponse->assertOk();
        $stickerResponse->assertSee($kiosk->machine_code);
        $stickerResponse->assertSee('الطباعة الفورية السريعة');
    }

    public function test_kiosk_screen_terminal_loads_and_polls(): void
    {
        $kiosk = $this->setupKiosk();

        // Screen HTML
        $response = $this->get(route('kiosk.screen', $kiosk->machine_code));
        $response->assertOk();
        $response->assertSee($kiosk->machine_code);
        $response->assertSee('الطباعة الذاتية الفورية');

        // Screen Poll Status
        $pollResponse = $this->get(route('api.kiosk.screen.status', $kiosk->machine_code));
        $pollResponse->assertOk();
        $pollResponse->assertJsonStructure([
            'machine_code',
            'status',
            'paper_a4',
            'session' => ['token', 'status'],
        ]);
    }

    public function test_kiosk_mobile_flow_and_constraints(): void
    {
        Storage::fake('public');
        $kiosk = $this->setupKiosk();

        // 1. Mobile Connect View
        $connectResponse = $this->get(route('kiosk.mobile.connect', $kiosk->qr_token));
        $connectResponse->assertOk();
        $connectResponse->assertSee($kiosk->machine_code);

        $session = KioskSession::where('kiosk_machine_id', $kiosk->id)->first();
        $this->assertNotNull($session);

        // 2. Upload with valid image
        $file = UploadedFile::fake()->image('test_doc.jpg', 600, 600);
        $uploadResponse = $this->post(route('kiosk.mobile.upload', $session->session_token), [
            'file' => $file,
            'copies' => 1,
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
        ]);

        $uploadResponse->assertOk();
        $uploadResponse->assertJsonPath('success', true);

        // Minimum order price was enforced (0.50 -> 2.00 SAR + VAT)
        $this->assertEquals(2.30, $uploadResponse->json('data.total_price'));

        // 3. Confirm & Pay
        $payResponse = $this->post(route('kiosk.mobile.pay', $session->session_token), [
            'payment_method' => 'card',
        ]);

        $payResponse->assertOk();
        $payResponse->assertJsonPath('success', true);

        // Check that paper tray was decremented
        $kiosk->refresh();
        $this->assertEquals(199, $kiosk->paper_tray_a4_sheets);
    }

    public function test_mobile_api_flow_updates_kiosk_screen_states(): void
    {
        Storage::fake('public');
        $kiosk = $this->setupKiosk();
        $user = User::create([
            'name' => 'عميل تجريبي',
            'phone' => '050'.rand(1000000, 9999999),
            'phone_verified_at' => now(),
            'status' => 'active',
            'wallet_balance' => 100.00,
        ]);

        // 1. Mobile calls API connect endpoint (Scans QR)
        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $connectRes->assertOk();
        $connectRes->assertJsonPath('success', true);
        $sessionToken = $connectRes->json('data.session_token');
        $this->assertNotEmpty($sessionToken);

        // Screen is now 'connected'
        $session = KioskSession::where('session_token', $sessionToken)->first();
        $this->assertEquals('connected', $session->status);

        // 2. Mobile uploads file via /api/v1/files/upload-and-inspect
        $file = UploadedFile::fake()->image('my_cv.png', 400, 400);
        $uploadRes = $this->postJson('/api/v1/files/upload-and-inspect', [
            'file' => $file,
            'session_token' => $sessionToken,
            'copies' => 2,
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
        ]);
        $uploadRes->assertOk();
        $uploadRes->assertJsonPath('data.kiosk_screen_updated', true);

        // Screen is now 'file_uploaded' with file details
        $session->refresh();
        $this->assertEquals('file_uploaded', $session->status);
        $this->assertEquals('my_cv.png', $session->file_name);
        $this->assertEquals(2, $session->copies_count);

        // 3. Mobile calculates price & indicates checkout step
        $calcRes = $this->postJson('/api/v1/files/calculate-price', [
            'service_type' => 'self_printing',
            'detected_page_count' => 1,
            'copies_count' => 2,
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'session_token' => $sessionToken,
            'is_checkout_step' => true,
        ]);
        $calcRes->assertOk();
        $calcRes->assertJsonPath('data.kiosk_screen_status', 'awaiting_payment');
        $this->assertEquals(2.30, $calcRes->json('data.total_amount'));

        // Screen is now 'awaiting_payment'
        $session->refresh();
        $this->assertEquals('awaiting_payment', $session->status);
        $this->assertEquals(2.30, $session->total_price);

        // 4. Mobile user completes checkout using ONLY session_token and payment_method
        $checkoutRes = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders/self-print/checkout', [
            'session_token' => $sessionToken,
            'payment_method' => 'card',
        ]);
        $checkoutRes->assertOk();

        // Screen is now 'printing'
        $session->refresh();
        $this->assertEquals('printing', $session->status);
        $this->assertNotNull($session->order_id);
    }

    public function test_kiosk_extend_session_and_timeout_cancellation(): void
    {
        $kiosk = $this->setupKiosk();

        // 1. Connect
        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $connectRes->assertOk();
        $sessionToken = $connectRes->json('data.session_token');

        // 2. Extend Session (Keep-Alive)
        $extendRes = $this->postJson('/api/v1/kiosks/extend-session', [
            'session_token' => $sessionToken,
        ]);
        $extendRes->assertOk();
        $extendRes->assertJsonPath('success', true);
        $extendRes->assertJsonPath('data.session_token', $sessionToken);

        // 3. Inactive Timeout Cancellation (User idle > 30s + 10s modal countdown expired)
        $resetRes = $this->postJson("/kiosk/complete-reset/{$sessionToken}", [
            'status' => 'cancelled',
        ]);
        $resetRes->assertOk();
        $resetRes->assertJsonStructure(['success', 'new_token']);

        $oldSession = KioskSession::where('session_token', $sessionToken)->first();
        $this->assertEquals('cancelled', $oldSession->status);

        // A new fresh session is ready on the screen in waiting_scan state
        $newSession = KioskSession::where('session_token', $resetRes->json('new_token'))->first();
        $this->assertNotNull($newSession);
        $this->assertEquals('waiting_scan', $newSession->status);
    }
}
