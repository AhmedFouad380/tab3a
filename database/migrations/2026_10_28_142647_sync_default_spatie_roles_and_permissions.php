<?php

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Seed or ensure all permissions exist for guard 'admin'
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

        // 2. Define or update roles and their default permissions
        // Super Admin
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'admin',
        ]);
        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());

        // Admin
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'admin',
        ]);
        if ($adminRole->permissions()->count() === 0) {
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
        }

        // Branch Manager (Branch Staff)
        $branchManagerRole = Role::firstOrCreate([
            'name' => 'branch_manager',
            'guard_name' => 'admin',
        ]);
        if ($branchManagerRole->permissions()->count() === 0) {
            $branchManagerRole->syncPermissions([
                'view_orders',
                'manage_orders',
                'download_order_files',
                'manage_kiosks',
            ]);
        }

        // Support Agent
        $supportRole = Role::firstOrCreate([
            'name' => 'support_agent',
            'guard_name' => 'admin',
        ]);
        if ($supportRole->permissions()->count() === 0) {
            $supportRole->syncPermissions([
                'view_orders',
                'download_order_files',
                'manage_users',
                'manage_settings',
            ]);
        }

        // 3. Ensure existing admins have their roles attached if missing
        $admins = Admin::all();
        foreach ($admins as $admin) {
            if ($admin->roles()->count() === 0 && ! empty($admin->role)) {
                $role = Role::where('name', $admin->role)->where('guard_name', 'admin')->first();
                if ($role) {
                    $admin->assignRole($role);
                }
            }
        }

        // Final cache reset
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for safety
    }
};
