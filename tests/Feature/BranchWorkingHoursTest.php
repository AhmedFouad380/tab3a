<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\BranchWorkingHour;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchWorkingHoursTest extends TestCase
{
    use RefreshDatabase;

    private function createBranch(array $attributes = []): Branch
    {
        return Branch::create(array_merge([
            'name' => ['ar' => 'فرع الواحة', 'en' => 'Al-Waha Branch'],
            'address' => ['ar' => 'شارع الملك عبدالله', 'en' => 'King Abdullah St'],
            'city' => 'الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
            'allows_pre_order' => true,
            'daily_capacity' => 100,
        ], $attributes));
    }

    public function test_branch_sync_working_hours_creates_records_for_selected_days(): void
    {
        $branch = $this->createBranch();

        $schedules = [
            [
                'days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'],
                'opening_time' => '09:00',
                'closing_time' => '23:00',
                'is_day_off' => false,
            ],
            [
                'days' => ['friday'],
                'is_day_off' => true,
            ],
        ];

        $branch->syncWorkingHours($schedules);

        $this->assertDatabaseCount('branch_working_hours', 6);

        $this->assertDatabaseHas('branch_working_hours', [
            'branch_id' => $branch->id,
            'day_of_week' => 'sunday',
            'opening_time' => '09:00:00',
            'closing_time' => '23:00:00',
            'is_day_off' => false,
        ]);

        $this->assertDatabaseHas('branch_working_hours', [
            'branch_id' => $branch->id,
            'day_of_week' => 'friday',
            'is_day_off' => true,
        ]);
    }

    public function test_branch_reconstructs_schedules_from_stored_working_hours(): void
    {
        $branch = $this->createBranch();

        $schedules = [
            [
                'days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'],
                'opening_time' => '08:00',
                'closing_time' => '22:00',
                'is_day_off' => false,
            ],
            [
                'days' => ['saturday'],
                'opening_time' => '10:00',
                'closing_time' => '18:00',
                'is_day_off' => false,
            ],
        ];

        $branch->syncWorkingHours($schedules);

        $reconstructed = $branch->getWorkingHoursSchedules();

        $this->assertCount(2, $reconstructed);
        $this->assertEquals(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'], $reconstructed[0]['days']);
        $this->assertEquals('08:00', $reconstructed[0]['opening_time']);
        $this->assertEquals('22:00', $reconstructed[0]['closing_time']);

        $this->assertEquals(['saturday'], $reconstructed[1]['days']);
        $this->assertEquals('10:00', $reconstructed[1]['opening_time']);
        $this->assertEquals('18:00', $reconstructed[1]['closing_time']);
    }

    public function test_branch_working_hours_summary_attribute(): void
    {
        $branch = $this->createBranch();

        $branch->syncWorkingHours([
            [
                'days' => ['sunday', 'monday', 'tuesday'],
                'opening_time' => '08:00',
                'closing_time' => '16:00',
                'is_day_off' => false,
            ],
        ]);

        $summary = $branch->working_hours_summary;

        $this->assertStringContainsString('08:00 - 16:00', $summary);
        $this->assertStringContainsString('الأحد', $summary);
    }

    public function test_is_open_now_evaluates_correctly(): void
    {
        $branch = $this->createBranch();

        // Branch open on Thursdays between 08:00 and 18:00
        BranchWorkingHour::create([
            'branch_id' => $branch->id,
            'day_of_week' => 'thursday',
            'opening_time' => '08:00:00',
            'closing_time' => '18:00:00',
            'is_day_off' => false,
        ]);

        // Branch closed on Fridays
        BranchWorkingHour::create([
            'branch_id' => $branch->id,
            'day_of_week' => 'friday',
            'opening_time' => '00:00:00',
            'closing_time' => '00:00:00',
            'is_day_off' => true,
        ]);

        // Thursday at 12:00 PM -> Open
        Carbon::setTestNow(Carbon::parse('2026-10-15 12:00:00', 'Asia/Riyadh'));
        $this->assertTrue($branch->isOpenNow());

        // Thursday at 20:00 PM -> Closed (after closing time)
        Carbon::setTestNow(Carbon::parse('2026-10-15 20:00:00', 'Asia/Riyadh'));
        $this->assertFalse($branch->isOpenNow());

        // Friday at 12:00 PM -> Closed (Day off)
        Carbon::setTestNow(Carbon::parse('2026-10-16 12:00:00', 'Asia/Riyadh'));
        $this->assertFalse($branch->isOpenNow());

        Carbon::setTestNow(); // Reset test time
    }

    public function test_api_branches_returns_working_hours_and_is_open(): void
    {
        $branch = $this->createBranch();
        $branch->syncWorkingHours([
            [
                'days' => ['sunday', 'monday'],
                'opening_time' => '09:00',
                'closing_time' => '21:00',
                'is_day_off' => false,
            ],
        ]);

        $response = $this->getJson('/api/v1/branches');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'is_open',
                        'working_hours_summary',
                        'working_hours',
                    ],
                ],
            ]);
    }
}
