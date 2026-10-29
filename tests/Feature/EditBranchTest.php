<?php

namespace Tests\Feature;

use App\Filament\Resources\BranchResource\Pages\EditBranch;
use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EditBranchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_save_working_hours_schedules(): void
    {
        $permission = Permission::findOrCreate('manage_branches', 'admin');
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
        $admin->givePermissionTo($permission);

        $branch = Branch::create([
            'name' => ['ar' => 'فرع تجريبي', 'en' => 'Test Branch'],
            'address' => ['ar' => 'العنوان', 'en' => 'Address'],
            'city' => 'الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
            'allows_pre_order' => true,
        ]);

        $this->actingAs($admin, 'admin');

        $schedules = [
            [
                'days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'saturday'],
                'opening_time' => '08:00',
                'closing_time' => '12:00',
                'is_day_off' => false,
            ],
            [
                'days' => ['friday'],
                'opening_time' => '16:00',
                'closing_time' => '23:30',
                'is_day_off' => false,
            ],
        ];

        Livewire::test(EditBranch::class, ['record' => $branch->getRouteKey()])
            ->fillForm([
                'working_hours_schedules' => $schedules,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $branch->refresh();
        $this->assertCount(7, $branch->workingHours);

        $friday = $branch->workingHours()->where('day_of_week', 'friday')->first();
        $this->assertNotNull($friday);
        $this->assertFalse((bool) $friday->is_day_off);
        $this->assertEquals('16:00:00', $friday->opening_time);
        $this->assertEquals('23:30:00', $friday->closing_time);
    }
}
