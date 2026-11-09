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
        $response->assertSee('خدمة الطباعة الذاتية');

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

    public function test_kiosk_mobile_disconnect_cancels_session_and_resets_kiosk_screen_to_home(): void
    {
        $kiosk = $this->setupKiosk();

        // 1. Mobile connects to kiosk
        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $connectRes->assertOk();
        $sessionToken = $connectRes->json('data.session_token');

        $activeSession = KioskSession::where('session_token', $sessionToken)->first();
        $this->assertEquals('connected', $activeSession->status);

        // 2. Mobile disconnects via POST /api/v1/kiosks/disconnect
        $disconnectRes = $this->postJson('/api/v1/kiosks/disconnect', [
            'session_token' => $sessionToken,
        ]);
        $disconnectRes->assertOk();
        $disconnectRes->assertJsonPath('success', true);
        $disconnectRes->assertJsonPath('data.status', 'cancelled');
        $disconnectRes->assertJsonPath('data.kiosk_screen_status', 'waiting_scan');
        $newSessionToken = $disconnectRes->json('data.new_session_token');
        $this->assertNotEmpty($newSessionToken);
        $this->assertNotEquals($sessionToken, $newSessionToken);

        // 3. Verify original session was terminated and marked cancelled
        $activeSession->refresh();
        $this->assertEquals('cancelled', $activeSession->status);
        $this->assertNotNull($activeSession->completed_at);

        // 4. Verify new standby session exists with status waiting_scan
        $newSession = KioskSession::where('session_token', $newSessionToken)->first();
        $this->assertNotNull($newSession);
        $this->assertEquals('waiting_scan', $newSession->status);

        // 5. Verify the kiosk screen polling endpoint now delivers waiting_scan
        $screenPollRes = $this->getJson(route('api.kiosk.screen.status', $kiosk->machine_code));
        $screenPollRes->assertOk();
        $screenPollRes->assertJsonPath('session.status', 'waiting_scan');
        $screenPollRes->assertJsonPath('session.token', $newSessionToken);
    }

    public function test_kiosk_disconnect_using_machine_code(): void
    {
        $kiosk = $this->setupKiosk();

        // 1. Mobile connects
        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $sessionToken = $connectRes->json('data.session_token');

        // 2. Disconnect using machine_code
        $disconnectRes = $this->postJson('/api/v1/kiosks/cancel-session', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $disconnectRes->assertOk();
        $disconnectRes->assertJsonPath('success', true);
        $disconnectRes->assertJsonPath('data.kiosk_screen_status', 'waiting_scan');

        $session = KioskSession::where('session_token', $sessionToken)->first();
        $this->assertEquals('cancelled', $session->status);
    }

    public function test_kiosk_update_session_status_cancelled_resets_screen_to_home(): void
    {
        $kiosk = $this->setupKiosk();

        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $sessionToken = $connectRes->json('data.session_token');

        $updateRes = $this->postJson('/api/v1/kiosks/update-session-status', [
            'session_token' => $sessionToken,
            'status' => 'cancelled',
        ]);
        $updateRes->assertOk();
        $updateRes->assertJsonPath('success', true);
        $updateRes->assertJsonPath('data.kiosk_screen_status', 'waiting_scan');

        $session = KioskSession::where('session_token', $sessionToken)->first();
        $this->assertEquals('cancelled', $session->status);
        $this->assertNotNull($session->completed_at);
    }

    public function test_kiosk_rejects_color_printing_when_machine_does_not_support_color(): void
    {
        $kiosk = $this->setupKiosk();
        $kiosk->update(['supports_color' => false]);

        $user = User::create([
            'name' => 'عميل اختبار',
            'phone' => '050'.rand(1000000, 9999999),
            'phone_verified_at' => now(),
            'status' => 'active',
            'wallet_balance' => 50.00,
        ]);

        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $sessionToken = $connectRes->json('data.session_token');

        $session = KioskSession::where('session_token', $sessionToken)->first();
        $session->update([
            'file_path' => 'uploads/test.jpg',
            'file_name' => 'test.jpg',
            'color_mode' => 'color',
        ]);

        // Attempt checkout with color on B&W-only machine
        $checkoutRes = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders/self-print/checkout', [
            'session_token' => $sessionToken,
            'payment_method' => 'card',
            'color_mode' => 'color',
        ]);

        $checkoutRes->assertStatus(422);
        $this->assertStringContainsString('تدعم الطباعة بالأبيض والأسود فقط', $checkoutRes->json('message'));
    }

    public function test_kiosk_rejects_color_when_color_toner_is_low_but_allows_black_and_white(): void
    {
        $kiosk = $this->setupKiosk();
        // Cyan toner empty, but black toner is 100%
        $kiosk->update([
            'black_toner_level' => 100,
            'cyan_toner_level' => 2,
            'magenta_toner_level' => 80,
            'yellow_toner_level' => 80,
        ]);

        $user = User::create([
            'name' => 'عميل اختبار',
            'phone' => '050'.rand(1000000, 9999999),
            'phone_verified_at' => now(),
            'status' => 'active',
            'wallet_balance' => 50.00,
        ]);

        // 1. Verify machine with color -> should fail toner check
        $verifyColorRes = $this->postJson('/api/v1/kiosks/verify-machine', [
            'machine_code' => $kiosk->machine_code,
            'total_sheets_needed' => 2,
            'paper_size' => 'A4',
            'color_mode' => 'color',
            'side_mode' => 'single_sided',
        ]);
        $verifyColorRes->assertOk();
        $verifyColorRes->assertJsonPath('data.checks.toner_passed', false);
        $verifyColorRes->assertJsonPath('data.checks.is_ready_to_print', false);

        // 2. Verify machine with black and white -> MUST PASS!
        $verifyBwRes = $this->postJson('/api/v1/kiosks/verify-machine', [
            'machine_code' => $kiosk->machine_code,
            'total_sheets_needed' => 2,
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
        ]);
        $verifyBwRes->assertOk();
        $verifyBwRes->assertJsonPath('data.checks.toner_passed', true);
        $verifyBwRes->assertJsonPath('data.checks.is_ready_to_print', true);

        // 3. Checkout with color fails
        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $sessionToken = $connectRes->json('data.session_token');

        $session = KioskSession::where('session_token', $sessionToken)->first();
        $session->update([
            'file_path' => 'uploads/doc.jpg',
            'file_name' => 'doc.jpg',
        ]);

        $colorCheckout = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders/self-print/checkout', [
            'session_token' => $sessionToken,
            'payment_method' => 'card',
            'color_mode' => 'color',
        ]);
        $colorCheckout->assertStatus(422);
        $this->assertStringContainsString('مستوى الحبر الملون في الماكينة غير كافٍ', $colorCheckout->json('message'));

        // 4. Checkout with black and white SUCCEEDS!
        $bwCheckout = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders/self-print/checkout', [
            'session_token' => $sessionToken,
            'payment_method' => 'card',
            'color_mode' => 'black_and_white',
        ]);
        $bwCheckout->assertOk();
    }

    public function test_kiosk_verify_machine_returns_accurate_capacities_and_page_counts(): void
    {
        $kiosk = $this->setupKiosk();
        $kiosk->update([
            'paper_tray_a4_sheets' => 75,
            'paper_tray_a3_sheets' => 20,
        ]);

        $verifyRes = $this->postJson('/api/v1/kiosks/verify-machine', [
            'machine_code' => $kiosk->machine_code,
            'total_sheets_needed' => 10,
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
        ]);

        $verifyRes->assertOk();
        $capacity = $verifyRes->json('data.capacity');
        $this->assertEquals(75, $capacity['tray_sheets_available']);
        $this->assertEquals(75, $capacity['max_pages_single_sided']);
        $this->assertEquals(150, $capacity['max_pages_double_sided']); // 75 sheets * 2
        $this->assertTrue($capacity['is_bw_ready']);
    }

    public function test_document_print_preparation_service_converts_image_to_grayscale_for_bw_order(): void
    {
        Storage::fake('public');
        $kiosk = $this->setupKiosk();
        $user = User::create([
            'name' => 'عميل اختبار',
            'phone' => '050'.rand(1000000, 9999999),
            'phone_verified_at' => now(),
            'status' => 'active',
            'wallet_balance' => 50.00,
        ]);

        // Upload a color image
        $file = UploadedFile::fake()->image('colored_poster.png', 400, 400);
        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $sessionToken = $connectRes->json('data.session_token');

        $uploadRes = $this->postJson('/api/v1/files/upload-and-inspect', [
            'file' => $file,
            'session_token' => $sessionToken,
            'color_mode' => 'black_and_white',
        ]);
        $uploadRes->assertOk();

        // Checkout with black_and_white
        $checkoutRes = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders/self-print/checkout', [
            'session_token' => $sessionToken,
            'payment_method' => 'card',
            'color_mode' => 'black_and_white',
        ]);
        $checkoutRes->assertOk();

        // Verify session and order item have print_file_path pointing to a prepared print file
        $session = KioskSession::where('session_token', $sessionToken)->first();
        $this->assertNotNull($session->print_file_path);
        $this->assertStringContainsString('print_ready', $session->print_file_path);

        // Screen status polling delivers print_file_path as file_url
        $pollRes = $this->getJson(route('api.kiosk.screen.status', $kiosk->machine_code));
        $pollRes->assertOk();
        $this->assertStringContainsString('print_ready', $pollRes->json('session.file_url'));
    }

    public function test_upload_and_inspect_rejects_when_sheets_exceed_kiosk_max_limit(): void
    {
        Storage::fake('public');
        $kiosk = $this->setupKiosk();
        // Limit kiosk to 1 sheet max
        $kiosk->update(['max_sheets_per_job' => 1]);

        $connectRes = $this->postJson('/api/v1/kiosks/connect', [
            'machine_code' => $kiosk->machine_code,
        ]);
        $sessionToken = $connectRes->json('data.session_token');

        // Upload an image with 5 copies -> 5 sheets > 1 sheet allowed
        $file = UploadedFile::fake()->image('document.jpg', 300, 300);
        $uploadRes = $this->postJson('/api/v1/files/upload-and-inspect', [
            'file' => $file,
            'session_token' => $sessionToken,
            'copies' => 5,
        ]);

        $uploadRes->assertStatus(422);
        $this->assertStringContainsString('الحد الأقصى المسموح به للطباعة الفورية', $uploadRes->json('message'));
        $this->assertEquals(5, $uploadRes->json('errors.total_sheets_needed'));
        $this->assertEquals(1, $uploadRes->json('errors.max_allowed_sheets'));
    }

    public function test_calculate_price_rejects_when_sheets_exceed_kiosk_max_limit(): void
    {
        $kiosk = $this->setupKiosk();
        $kiosk->update(['max_sheets_per_job' => 2]);

        $calcRes = $this->postJson('/api/v1/files/calculate-price', [
            'service_type' => 'self_printing',
            'kiosk_machine_id' => $kiosk->id,
            'detected_page_count' => 5,
            'copies_count' => 1,
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
        ]);

        $calcRes->assertStatus(422);
        $this->assertStringContainsString('الحد الأقصى المسموح به للطباعة الفورية', $calcRes->json('message'));
    }
}
