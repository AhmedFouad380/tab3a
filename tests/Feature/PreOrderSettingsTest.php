<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\PricingRule;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreOrderSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        SystemSetting::updateOrCreate(
            ['key' => 'pre_order_max_sheets'],
            [
                'group' => 'print_settings',
                'type' => 'string',
                'display_name' => ['ar' => 'الحد الأقصى لعدد الأوراق للطباعة المسبقة', 'en' => 'Max Sheets for Pre-Order'],
                'value' => ['ar' => '200', 'en' => '200'],
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'pre_order_min_order_price'],
            [
                'group' => 'pricing',
                'type' => 'string',
                'display_name' => ['ar' => 'الحد الأدنى لقيمة طلب الطباعة المسبقة (ر.س)', 'en' => 'Minimum Pre-Order Price (SAR)'],
                'value' => ['ar' => '10.00', 'en' => '10.00'],
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'tax_rate_percentage'],
            [
                'group' => 'pricing',
                'type' => 'string',
                'display_name' => ['ar' => 'ضريبة القيمة المضافة', 'en' => 'VAT'],
                'value' => ['ar' => '15', 'en' => '15'],
            ]
        );

        PricingRule::create([
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'service_type' => 'pre_order',
            'price_per_page' => 0.50,
            'is_active' => true,
        ]);
    }

    public function test_get_settings_api_returns_pre_order_settings(): void
    {
        $response = $this->getJson('/api/v1/settings');

        $response->assertOk();
        $response->assertJsonPath('data.pre_order_max_sheets', '200');
        $response->assertJsonPath('data.pre_order_min_order_price', '10.00');
    }

    public function test_calculate_price_enforces_pre_order_min_order_price(): void
    {
        // 2 pages x 1 copy @ 0.50 = 1.00 SAR (below 10.00 SAR min price)
        $response = $this->postJson('/api/v1/files/calculate-price', [
            'service_type' => 'pre_order',
            'detected_page_count' => 2,
            'copies_count' => 1,
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
        ]);

        $response->assertOk();
        // Subtotal should be raised to 10.00 SAR min price
        $response->assertJsonPath('data.subtotal', 10);
        // Tax is 15% of 10.00 = 1.50 SAR
        $response->assertJsonPath('data.tax_amount', 1.50);
        // Total amount is 11.50 SAR
        $response->assertJsonPath('data.total_amount', 11.50);
    }

    public function test_calculate_price_rejects_when_sheets_exceed_pre_order_max_limit(): void
    {
        // 150 pages x 2 copies = 300 sheets > 200 max allowed sheets
        $response = $this->postJson('/api/v1/files/calculate-price', [
            'service_type' => 'pre_order',
            'detected_page_count' => 150,
            'copies_count' => 2,
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $this->assertStringContainsString('الحد الأقصى المسموح به للطباعة المسبقة', $response->json('message'));
        $this->assertEquals(300, $response->json('errors.total_sheets_needed'));
        $this->assertEquals(200, $response->json('errors.max_allowed_sheets'));
    }

    public function test_create_pre_order_enforces_min_price_and_max_sheets(): void
    {
        $user = User::create([
            'name' => 'عميل طباعة مسبقة',
            'phone' => '0559988776',
            'phone_verified_at' => now(),
            'status' => 'active',
            'wallet_balance' => 100.00,
        ]);

        $branch = Branch::create([
            'name' => ['ar' => 'فرع العليا', 'en' => 'Olaya Branch'],
            'address' => ['ar' => 'الرياض', 'en' => 'Riyadh'],
            'city' => 'الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'allows_pre_order' => true,
            'is_active' => true,
        ]);

        // 1. Exceeding max sheets should be rejected with 422
        $rejectResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders/pre-order/checkout', [
            'branch_id' => $branch->id,
            'scheduled_pickup_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'file_path' => 'uploads/test_doc.pdf',
            'original_file_name' => 'test_doc.pdf',
            'file_extension' => 'pdf',
            'file_size_bytes' => 10240,
            'detected_page_count' => 250,
            'copies_count' => 1, // 250 sheets > 200 max
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'payment_method' => 'wallet',
        ]);

        $rejectResponse->assertStatus(422);
        $this->assertStringContainsString('الحد الأقصى المسموح به للطباعة المسبقة', $rejectResponse->json('message'));

        // 2. Small order (2 pages = 1.00 SAR) should enforce 10.00 SAR minimum price + VAT
        $successResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders/pre-order/checkout', [
            'branch_id' => $branch->id,
            'scheduled_pickup_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'file_path' => 'uploads/small_doc.pdf',
            'original_file_name' => 'small_doc.pdf',
            'file_extension' => 'pdf',
            'file_size_bytes' => 5120,
            'detected_page_count' => 2,
            'copies_count' => 1,
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'payment_method' => 'wallet',
        ]);

        $successResponse->assertOk();
        $this->assertEquals(10.00, $successResponse->json('data.subtotal'));
        $this->assertEquals(1.50, $successResponse->json('data.tax_amount'));
        $this->assertEquals(11.50, $successResponse->json('data.total_amount'));

        // User wallet should be deducted by totalAmount (100 - 11.50 = 88.50)
        $user->refresh();
        $this->assertEquals(88.50, (float) $user->wallet_balance);
    }
}
