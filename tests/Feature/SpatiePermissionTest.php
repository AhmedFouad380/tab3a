<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SpatiePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_default_roles_and_permissions_are_seeded_with_admin_guard(): void
    {
        $this->assertDatabaseHas('roles', [
            'name' => 'super_admin',
            'guard_name' => 'admin',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
            'guard_name' => 'admin',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'manage_orders',
            'guard_name' => 'admin',
        ]);
    }

    public function test_admin_can_have_roles_assigned_and_checked(): void
    {
        $admin = Admin::create([
            'name' => 'مشرف فرع تجريبي',
            'email' => 'branch_staff@test.com',
            'phone' => '0512345678',
            'password' => bcrypt('password'),
            'role' => 'branch_manager',
            'is_active' => true,
        ]);

        $this->assertTrue($admin->hasRole('branch_manager'));
        $this->assertTrue($admin->can('manage_orders'));
        $this->assertTrue($admin->can('view_orders'));
        $this->assertFalse($admin->can('manage_pricing'));
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $admin = Admin::create([
            'name' => 'المدير العام',
            'email' => 'super@test.com',
            'phone' => '0599999999',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->assertTrue($admin->hasRole('super_admin'));
        $this->assertTrue($admin->can('manage_orders'));
        $this->assertTrue($admin->can('manage_pricing'));
        $this->assertTrue($admin->can('manage_roles'));
        $this->assertTrue($admin->can('manage_admins'));
        $this->assertTrue($admin->can('manage_branches'));
    }

    public function test_user_model_supports_roles_with_web_guard(): void
    {
        $user = User::create([
            'name' => 'عميل مميز',
            'phone' => '0501234567',
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);

        $vipRole = Role::create([
            'name' => 'vip_customer',
            'guard_name' => 'web',
        ]);

        $user->assignRole($vipRole);

        $this->assertTrue($user->hasRole('vip_customer'));
    }

    public function test_super_admin_can_access_roles_index_page_in_filament(): void
    {
        $admin = Admin::create([
            'name' => 'مدير النظام',
            'email' => 'boss@test.com',
            'phone' => '0588888888',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/roles');

        $response->assertOk();
    }

    public function test_branch_manager_with_limited_permissions_cannot_access_unauthorized_filament_resources(): void
    {
        $branchStaff = Admin::create([
            'name' => 'مشرف فرع محدد الصلاحيات',
            'email' => 'staff_branch@test.com',
            'phone' => '0577777777',
            'password' => bcrypt('password'),
            'role' => 'branch_manager',
            'is_active' => true,
        ]);

        // Authorized resource (view_orders)
        $this->actingAs($branchStaff, 'admin')
            ->get('/admin/orders')
            ->assertOk();

        // Unauthorized resources
        $this->actingAs($branchStaff, 'admin')
            ->get('/admin/roles')
            ->assertForbidden();

        $this->actingAs($branchStaff, 'admin')
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($branchStaff, 'admin')
            ->get('/admin/pricing-rules')
            ->assertForbidden();

        $this->actingAs($branchStaff, 'admin')
            ->get('/admin/system-settings')
            ->assertForbidden();
    }
}
