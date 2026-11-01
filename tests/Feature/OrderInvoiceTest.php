<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\KioskMachine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): Admin
    {
        return Admin::create([
            'name' => 'مدير النظام',
            'email' => 'admin@test.com',
            'phone' => '0500000000',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'أحمد فؤاد',
            'phone' => '0501234567',
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);
    }

    private function createBranch(): Branch
    {
        return Branch::create([
            'name' => ['ar' => 'فرع الرياض - العليا', 'en' => 'Riyadh Branch - Olaya'],
            'address' => ['ar' => 'شارع التحلية، الرياض', 'en' => 'Tahlia St, Riyadh'],
            'city' => 'الرياض',
            'phone' => '0112345678',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_order_invoice(): void
    {
        $user = $this->createUser();
        $order = Order::create([
            'order_number' => 'ORD-1001',
            'user_id' => $user->id,
            'order_type' => 'pre_order',
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'mada',
            'subtotal' => 20.00,
            'tax_amount' => 3.00,
            'total_amount' => 23.00,
        ]);

        $response = $this->get(route('admin.orders.invoice', $order));
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_pre_order_invoice(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();
        $branch = $this->createBranch();

        $order = Order::create([
            'order_number' => 'ORD-PRE-2026',
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'order_type' => 'pre_order',
            'is_teacher' => true,
            'status' => 'ready_for_pickup',
            'payment_status' => 'paid',
            'payment_method' => 'apple_pay',
            'subtotal' => 50.00,
            'tax_amount' => 7.50,
            'discount_amount' => 5.00,
            'total_amount' => 52.50,
            'scheduled_pickup_at' => now()->addHours(3),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'original_file_name' => 'اوراق_عمل_مادة_العلوم.pdf',
            'file_path' => 'orders/files/science.pdf',
            'file_extension' => 'pdf',
            'file_size_bytes' => 2048,
            'detected_page_count' => 10,
            'pages_to_print_count' => 10,
            'copies_count' => 2,
            'total_sheets_needed' => 10,
            'paper_size' => 'a4',
            'color_mode' => 'color',
            'side_mode' => 'double_sided',
            'unit_price_per_page' => 2.50,
            'total_item_price' => 50.00,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.invoice', $order));

        $response->assertOk();
        $response->assertSee('فاتورة ضريبية مبسطة');
        $response->assertSee('ORD-PRE-2026');
        $response->assertSee('خدمة الطباعة المسبقة');
        $response->assertSee('طلب معلّم');
        $response->assertSee('فرع الرياض - العليا');
        $response->assertSee('اوراق_عمل_مادة_العلوم.pdf');
        $response->assertSee('52.50');
        $response->assertSee('ختم الفاتورة الإلكترونية');
    }

    public function test_admin_can_view_kiosk_self_printing_invoice(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();
        $branch = $this->createBranch();

        $kiosk = KioskMachine::create([
            'name' => ['ar' => 'كشك الرياض 1', 'en' => 'Riyadh Kiosk 1'],
            'machine_code' => 'KSK-999',
            'branch_id' => $branch->id,
            'location_description' => ['ar' => 'بجوار المدخل الرئيسي', 'en' => 'Near main entrance'],
            'ip_address' => '192.168.1.100',
            'status' => 'online',
            'qr_token' => 'kiosk-token-999',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-KIOSK-3001',
            'user_id' => $user->id,
            'kiosk_machine_id' => $kiosk->id,
            'order_type' => 'self_printing',
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'mada',
            'subtotal' => 10.00,
            'tax_amount' => 1.50,
            'total_amount' => 11.50,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'original_file_name' => 'تذكرة_طيران.pdf',
            'file_path' => 'kiosk/ticket.pdf',
            'file_extension' => 'pdf',
            'file_size_bytes' => 1024,
            'detected_page_count' => 1,
            'pages_to_print_count' => 1,
            'copies_count' => 1,
            'total_sheets_needed' => 1,
            'paper_size' => 'a4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'unit_price_per_page' => 10.00,
            'total_item_price' => 10.00,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.invoice', $order));

        $response->assertOk();
        $response->assertSee('فاتورة ضريبية مبسطة');
        $response->assertSee('ORD-KIOSK-3001');
        $response->assertSee('خدمة الطباعة الذاتية الفورية');
        $response->assertSee('كود الجهاز: KSK-999');
        $response->assertSee('تذكرة_طيران.pdf');
        $response->assertSee('11.50');
        $response->assertSee('ختم الفاتورة الإلكترونية');
    }
}
