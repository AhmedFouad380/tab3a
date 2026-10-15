<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions list with guard 'admin'
        $permissions = [
            'view_orders' => 'عرض طلبات الطباعة',
            'manage_orders' => 'إدارة وتحديث حالات الطلبات',
            'download_order_files' => 'تنزيل ومعاينة ملفات الطلبات',
            'manage_branches' => 'إدارة الفروع ومواعيد العمل',
            'manage_kiosks' => 'إدارة ومراقبة ماكينات الطباعة الذاتية',
            'manage_pricing' => 'إدارة قواعد التسعير وخيارات التشطيب',
            'manage_users' => 'إدارة حسابات ومحافظ العملاء',
            'manage_admins' => 'إدارة حسابات المدراء وفريق العمل',
            'manage_roles' => 'إدارة الأدوار والصلاحيات',
            'manage_settings' => 'إدارة إعدادات النظام والصفحات الثابتة',
        ];

        foreach ($permissions as $name => $label) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'admin',
            ]);
        }

        // Roles definition with guard 'admin'
        // 1. Super Admin
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'admin',
        ]);
        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());

        // 2. Admin
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'admin',
        ]);
        $adminRole->syncPermissions([
            'view_orders',
            'manage_orders',
            'download_order_files',
            'manage_branches',
            'manage_kiosks',
            'manage_pricing',
            'manage_users',
            'manage_settings',
        ]);

        // 3. Branch Manager
        $branchManagerRole = Role::firstOrCreate([
            'name' => 'branch_manager',
            'guard_name' => 'admin',
        ]);
        $branchManagerRole->syncPermissions([
            'view_orders',
            'manage_orders',
            'download_order_files',
            'manage_kiosks',
        ]);

        // 4. Support Agent
        $supportRole = Role::firstOrCreate([
            'name' => 'support_agent',
            'guard_name' => 'admin',
        ]);
        $supportRole->syncPermissions([
            'view_orders',
            'download_order_files',
            'manage_users',
            'manage_settings',
        ]);

        // Assign super_admin role to existing super admins
        $superAdmins = Admin::where('role', 'super_admin')->get();
        foreach ($superAdmins as $admin) {
            $admin->assignRole($superAdminRole);
        }

        // Assign roles to other admins if any
        $admins = Admin::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->assignRole($adminRole);
        }
    }
}
