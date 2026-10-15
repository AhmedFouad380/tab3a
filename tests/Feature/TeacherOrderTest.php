<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\KioskMachine;
use App\Models\PricingRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        PricingRule::create([
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'price_per_page' => 0.50,
            'is_active' => true,
        ]);
    }

    public function test_calculate_price_handles_is_teacher_flag(): void
    {
        $response = $this->postJson('/api/v1/files/calculate-price', [
            'service_type' => 'self_printing',
            'detected_page_count' => 10,
            'copies_count' => 1,
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'is_teacher' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_teacher' => true,
                ],
            ]);
    }

    public function test_user_can_create_self_print_order_as_teacher(): void
    {
        $user = User::create([
            'name' => 'معلم تجريبي',
            'phone' => '0501234567',
            'phone_verified_at' => now(),
            'status' => 'active',
            'wallet_balance' => 100.00,
        ]);

        $branch = Branch::create([
            'name' => ['ar' => 'فرع تجريبي', 'en' => 'Test Branch'],
            'address' => ['ar' => 'العليا', 'en' => 'Olaya'],
            'city' => 'الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
        ]);

        $kiosk = KioskMachine::create([
            'branch_id' => $branch->id,
            'machine_code' => 'KIOSK-01',
            'qr_token' => 'QR-TOKEN-123',
            'name' => ['ar' => 'ماكينة 1', 'en' => 'Kiosk 1'],
            'status' => 'online',
            'paper_tray_a4_remaining' => 500,
            'paper_tray_a3_remaining' => 500,
            'toner_black_level_percentage' => 100,
            'toner_color_level_percentage' => 100,
            'is_active' => true,
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/orders/self-print/checkout', [
                'kiosk_machine_id' => $kiosk->id,
                'file_path' => 'orders/files/test.pdf',
                'original_file_name' => 'test.pdf',
                'file_extension' => 'pdf',
                'file_size_bytes' => 1024,
                'detected_page_count' => 5,
                'copies_count' => 1,
                'paper_size' => 'A4',
                'color_mode' => 'black_and_white',
                'side_mode' => 'single_sided',
                'payment_method' => 'wallet',
                'is_teacher' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_teacher' => true,
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'is_teacher' => true,
            'order_type' => 'self_printing',
        ]);
    }

    public function test_user_can_create_pre_order_as_teacher(): void
    {
        $user = User::create([
            'name' => 'معلم تجريبي 2',
            'phone' => '0507654321',
            'phone_verified_at' => now(),
            'status' => 'active',
            'wallet_balance' => 100.00,
        ]);

        $branch = Branch::create([
            'name' => ['ar' => 'فرع تجريبي 2', 'en' => 'Test Branch 2'],
            'address' => ['ar' => 'السليمانية', 'en' => 'Sulaimaniya'],
            'city' => 'الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/orders/pre-order/checkout', [
                'branch_id' => $branch->id,
                'scheduled_pickup_at' => now()->addHours(3)->toDateTimeString(),
                'file_path' => 'orders/files/exam.pdf',
                'original_file_name' => 'exam.pdf',
                'file_extension' => 'pdf',
                'file_size_bytes' => 2048,
                'detected_page_count' => 10,
                'copies_count' => 2,
                'paper_size' => 'A4',
                'color_mode' => 'black_and_white',
                'side_mode' => 'single_sided',
                'payment_method' => 'wallet',
                'is_teacher' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_teacher' => true,
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'is_teacher' => true,
            'order_type' => 'pre_order',
        ]);
    }
}
