<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderFileDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

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

    private function createUser(string $phone = '0501234567'): User
    {
        return User::create([
            'name' => 'عميل تجريبي',
            'phone' => $phone,
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);
    }

    private function createOrderItem(Order $order, string $name, string $path): OrderItem
    {
        return OrderItem::create([
            'order_id' => $order->id,
            'original_file_name' => $name,
            'file_path' => $path,
            'file_extension' => 'pdf',
            'file_size_bytes' => 1024,
            'detected_page_count' => 1,
            'pages_to_print_count' => 1,
            'copies_count' => 1,
            'total_sheets_needed' => 1,
            'unit_price_per_page' => 1.00,
            'total_item_price' => 1.00,
        ]);
    }

    public function test_guest_is_redirected_when_accessing_admin_download(): void
    {
        $user = $this->createUser();
        $order = Order::create([
            'order_number' => 'ORD-100',
            'user_id' => $user->id,
            'order_type' => 'pre_order',
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'total_amount' => 10.00,
        ]);

        $response = $this->get('/admin/orders/'.$order->id.'/download');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_download_single_order_file(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        Storage::disk('public')->put('uploads/test_file.pdf', 'Dummy PDF content');

        $order = Order::create([
            'order_number' => 'ORD-101',
            'user_id' => $user->id,
            'order_type' => 'pre_order',
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'total_amount' => 15.00,
        ]);

        $item = $this->createOrderItem($order, 'ملف_الطباعة_التجريبي.pdf', 'uploads/test_file.pdf');

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.download', $order));

        $response->assertOk();
        $this->assertTrue($response->headers->has('content-disposition'));
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
    }

    public function test_admin_can_download_multiple_order_files_as_zip(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        Storage::disk('public')->put('uploads/doc1.pdf', 'Document 1 content');
        Storage::disk('public')->put('uploads/doc2.pdf', 'Document 2 content');

        $order = Order::create([
            'order_number' => 'ORD-102',
            'user_id' => $user->id,
            'order_type' => 'pre_order',
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'total_amount' => 30.00,
        ]);

        $this->createOrderItem($order, 'doc1.pdf', 'uploads/doc1.pdf');
        $this->createOrderItem($order, 'doc2.pdf', 'uploads/doc2.pdf');

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.download', $order));

        $response->assertOk();
        $this->assertStringContainsString('application/zip', $response->headers->get('content-type'));
    }

    public function test_admin_can_preview_order_item(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        Storage::disk('public')->put('uploads/preview.pdf', 'Preview PDF content');

        $order = Order::create([
            'order_number' => 'ORD-103',
            'user_id' => $user->id,
            'order_type' => 'pre_order',
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'total_amount' => 10.00,
        ]);

        $item = $this->createOrderItem($order, 'preview.pdf', 'uploads/preview.pdf');

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.items.preview', $item));

        $response->assertOk();
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    public function test_api_user_can_download_their_order_file(): void
    {
        $user = $this->createUser();
        Storage::disk('public')->put('uploads/api_doc.pdf', 'API Doc content');

        $order = Order::create([
            'order_number' => 'ORD-104',
            'user_id' => $user->id,
            'order_type' => 'pre_order',
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'total_amount' => 10.00,
        ]);

        $item = $this->createOrderItem($order, 'api_doc.pdf', 'uploads/api_doc.pdf');

        $response = $this->actingAs($user, 'sanctum')->get('/api/v1/orders/'.$order->id.'/download');

        $response->assertOk();
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
    }

    public function test_api_user_cannot_download_another_users_order_file(): void
    {
        $user1 = $this->createUser('0501111111');
        $user2 = $this->createUser('0502222222');

        Storage::disk('public')->put('uploads/secret.pdf', 'Secret content');

        $order = Order::create([
            'order_number' => 'ORD-105',
            'user_id' => $user1->id,
            'order_type' => 'pre_order',
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'total_amount' => 10.00,
        ]);

        $item = $this->createOrderItem($order, 'secret.pdf', 'uploads/secret.pdf');

        $response = $this->actingAs($user2, 'sanctum')->get('/api/v1/orders/'.$order->id.'/download');

        $response->assertStatus(404);
    }
}
