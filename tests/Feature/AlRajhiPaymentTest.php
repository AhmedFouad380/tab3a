<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\KioskMachine;
use App\Models\KioskSession;
use App\Models\Order;
use App\Models\User;
use App\Services\Payment\AlRajhiPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AlRajhiPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.alrajhi.tranportal_id', 'TEST_TRAN_ID');
        Config::set('services.alrajhi.password', 'TEST_PASS_123');
        Config::set('services.alrajhi.resource_key', '1234567890123456'); // 16 bytes key
        Config::set('services.alrajhi.currency_code', '682');
        Config::set('services.alrajhi.base_url', 'https://securepayments.alrajhibank.com.sa/pg/payment/rest/');
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'عبدالله السبيعي',
            'phone' => '0509998877',
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);
    }

    private function createOrder(User $user, string $status = 'pending', string $paymentStatus = 'unpaid'): Order
    {
        $branch = Branch::create([
            'name' => ['ar' => 'فرع الرياض', 'en' => 'Riyadh Branch'],
            'address' => ['ar' => 'شارع الملك فهد', 'en' => 'King Fahd Road'],
            'city' => 'الرياض',
            'phone' => '0119998877',
            'is_active' => true,
        ]);

        return Order::create([
            'order_number' => 'ORD-RAJHI-'.uniqid(),
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'order_type' => 'pre_order',
            'status' => $status,
            'payment_status' => $paymentStatus,
            'payment_method' => 'alrajhi',
            'subtotal' => 50.00,
            'tax_amount' => 7.50,
            'discount_amount' => 0.00,
            'delivery_fee' => 0.00,
            'total_amount' => 57.50,
        ]);
    }

    public function test_service_can_encrypt_and_decrypt_payload(): void
    {
        $service = new AlRajhiPaymentService;

        $originalData = [
            'id' => 'TEST_ID',
            'amt' => '100.00',
            'trackId' => 'ORD-999',
            'result' => 'CAPTURED',
        ];

        $encrypted = $service->encrypt($originalData);
        $this->assertIsString($encrypted);
        $this->assertTrue(ctype_xdigit($encrypted));

        $decrypted = $service->decrypt($encrypted);
        $this->assertEquals($originalData['id'], $decrypted['id']);
        $this->assertEquals($originalData['amt'], $decrypted['amt']);
        $this->assertEquals($originalData['trackId'], $decrypted['trackId']);
        $this->assertEquals($originalData['result'], $decrypted['result']);
    }

    public function test_checkout_redirects_to_alrajhi_payment_url(): void
    {
        $user = $this->createUser();
        $order = $this->createOrder($user);

        $service = new AlRajhiPaymentService;
        $mockDecryptedResponse = [
            'result' => 'SUCCESS',
            'paymentId' => 'PAY-RAJHI-12345',
            'paymentURL' => 'https://securepayments.alrajhibank.com.sa/pg/checkout?id=PAY-RAJHI-12345',
        ];
        $mockEncrypted = $service->encrypt($mockDecryptedResponse);

        Http::fake([
            'https://securepayments.alrajhibank.com.sa/pg/payment/rest/performTransaction' => Http::response([
                'trandata' => $mockEncrypted,
            ], 200),
        ]);

        $response = $this->get(route('payment.alrajhi.checkout', $order));

        $response->assertRedirect('https://securepayments.alrajhibank.com.sa/pg/checkout?id=PAY-RAJHI-12345');
    }

    public function test_checkout_redirects_if_order_already_paid(): void
    {
        $user = $this->createUser();
        $order = $this->createOrder($user, status: 'completed', paymentStatus: 'paid');

        $response = $this->get(route('payment.alrajhi.checkout', $order));

        $response->assertRedirect(route('admin.orders.invoice', $order));
    }

    public function test_callback_successful_payment_updates_order_and_creates_payment(): void
    {
        $user = $this->createUser();
        $order = $this->createOrder($user);

        $service = new AlRajhiPaymentService;
        $callbackPayload = [
            'result' => 'CAPTURED',
            'tranid' => 'ARB-TXN-777888',
            'trackId' => $order->order_number,
            'amt' => '57.50',
            'authCode' => 'AUTH123',
            'udf1' => (string) $order->id,
        ];

        $encryptedTrandata = $service->encrypt($callbackPayload);

        $response = $this->post(route('payment.alrajhi.callback'), [
            'trandata' => $encryptedTrandata,
        ]);

        $response->assertOk();
        $response->assertViewIs('payments.alrajhi.success');
        $response->assertSee('تم الدفع بنجاح');
        $response->assertSee($order->order_number);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'gateway' => 'alrajhi',
            'transaction_id' => 'ARB-TXN-777888',
            'status' => 'successful',
        ]);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => 'payment_success',
        ]);
    }

    public function test_callback_declined_payment_marks_payment_failed_and_keeps_order_unpaid(): void
    {
        $user = $this->createUser();
        $order = $this->createOrder($user);

        $service = new AlRajhiPaymentService;
        $callbackPayload = [
            'result' => 'DECLINED',
            'error_text' => 'Insufficient funds',
            'tranid' => 'ARB-FAIL-111222',
            'trackId' => $order->order_number,
            'amt' => '57.50',
            'udf1' => (string) $order->id,
        ];

        $encryptedTrandata = $service->encrypt($callbackPayload);

        $response = $this->post(route('payment.alrajhi.callback'), [
            'trandata' => $encryptedTrandata,
        ]);

        $response->assertOk();
        $response->assertViewIs('payments.alrajhi.failed');
        $response->assertSee('Insufficient funds');

        $order->refresh();
        $this->assertEquals('unpaid', $order->payment_status);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'gateway' => 'alrajhi',
            'status' => 'failed',
        ]);
    }

    public function test_api_initiate_payment_for_authenticated_user(): void
    {
        $user = $this->createUser();
        $order = $this->createOrder($user);

        Sanctum::actingAs($user);

        $service = new AlRajhiPaymentService;
        $mockDecryptedResponse = [
            'result' => 'SUCCESS',
            'paymentId' => 'PAY-API-999',
            'paymentURL' => 'https://securepayments.alrajhibank.com.sa/pg/checkout?id=PAY-API-999',
        ];
        $mockEncrypted = $service->encrypt($mockDecryptedResponse);

        Http::fake([
            'https://securepayments.alrajhibank.com.sa/pg/payment/rest/performTransaction' => Http::response([
                'trandata' => $mockEncrypted,
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/payments/alrajhi/initiate', [
            'order_id' => $order->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'payment_url' => 'https://securepayments.alrajhibank.com.sa/pg/checkout?id=PAY-API-999',
                'payment_id' => 'PAY-API-999',
                'order_number' => $order->order_number,
            ]);
    }

    public function test_self_print_order_and_kiosk_sync_with_alrajhi_callback(): void
    {
        $user = $this->createUser();
        $branch = Branch::create([
            'name' => ['ar' => 'فرع الرياض', 'en' => 'Riyadh Branch'],
            'address' => ['ar' => 'شارع التحلية', 'en' => 'Tahlia St'],
            'city' => 'الرياض',
            'phone' => '0112345678',
            'is_active' => true,
        ]);

        $kiosk = KioskMachine::create([
            'branch_id' => $branch->id,
            'machine_code' => 'KSK-ARB-01',
            'qr_token' => 'QR-ARB-001',
            'name' => ['ar' => 'كشك الراجحي', 'en' => 'Al Rajhi Kiosk'],
            'status' => 'online',
            'paper_tray_a4_sheets' => 200,
            'paper_tray_a3_sheets' => 50,
            'black_toner_level' => 100,
        ]);

        $session = KioskSession::create([
            'kiosk_machine_id' => $kiosk->id,
            'session_token' => 'sess_alrajhi_'.uniqid(),
            'status' => 'awaiting_payment',
            'file_name' => 'document.pdf',
            'file_path' => 'kiosk/document.pdf',
            'detected_page_count' => 5,
            'copies_count' => 1,
            'total_sheets' => 5,
            'total_price' => 5.00,
            'expires_at' => now()->addMinutes(10),
        ]);

        $order = Order::create([
            'order_number' => 'ORD-KSK-'.uniqid(),
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'kiosk_machine_id' => $kiosk->id,
            'order_type' => 'self_printing',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'alrajhi',
            'subtotal' => 4.35,
            'tax_amount' => 0.65,
            'total_amount' => 5.00,
        ]);

        $session->update(['order_id' => $order->id]);

        $service = new AlRajhiPaymentService;
        $callbackPayload = [
            'result' => 'CAPTURED',
            'tranid' => 'ARB-KSK-777',
            'trackId' => $order->order_number,
            'amt' => '5.00',
            'udf1' => (string) $order->id,
        ];
        $encryptedTrandata = $service->encrypt($callbackPayload);

        $response = $this->post(route('payment.alrajhi.callback'), [
            'trandata' => $encryptedTrandata,
        ]);

        $response->assertOk();
        $response->assertViewIs('payments.alrajhi.success');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('completed', $order->status);

        // Kiosk session must now be set to printing!
        $session->refresh();
        $this->assertEquals('printing', $session->status);
    }
}
