<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'latitude',
        'longitude',
        'phone',
        'whatsapp',
        'email',
        'is_active',
        'allows_pre_order',
        'daily_capacity',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'address' => 'array',
            'is_active' => 'boolean',
            'allows_pre_order' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function workingHours()
    {
        return $this->hasMany(BranchWorkingHour::class);
    }

    public function kiosks()
    {
        return $this->hasMany(KioskMachine::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    /**
     * Synchronize working hour schedules from the form repeater.
     * Supports multiple shifts per day (e.g., morning and evening shifts).
     */
    public function syncWorkingHours(array $schedules): void
    {
        $allDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        // Clean up previous records for this branch
        $this->workingHours()->delete();

        foreach ($schedules as $schedule) {
            $days = (array) ($schedule['days'] ?? []);
            $isDayOff = (bool) ($schedule['is_day_off'] ?? false);
            $opening = ! $isDayOff && ! empty($schedule['opening_time'])
                ? (strlen($schedule['opening_time']) === 5 ? $schedule['opening_time'].':00' : $schedule['opening_time'])
                : '00:00:00';
            $closing = ! $isDayOff && ! empty($schedule['closing_time'])
                ? (strlen($schedule['closing_time']) === 5 ? $schedule['closing_time'].':00' : $schedule['closing_time'])
                : '00:00:00';

            foreach ($days as $day) {
                if (! in_array($day, $allDays)) {
                    continue;
                }

                $this->workingHours()->create([
                    'day_of_week' => $day,
                    'opening_time' => $opening,
                    'closing_time' => $closing,
                    'is_day_off' => $isDayOff,
                ]);
            }
        }
    }

    /**
     * Group existing working hours into schedules for editing in Filament and API.
     */
    public function getWorkingHoursSchedules(): array
    {
        $dayOrder = [
            'sunday' => 1,
            'monday' => 2,
            'tuesday' => 3,
            'wednesday' => 4,
            'thursday' => 5,
            'friday' => 6,
            'saturday' => 7,
        ];

        $hours = $this->workingHours()
            ->get()
            ->sortBy(fn ($wh) => [
                $wh->opening_time,
                $dayOrder[$wh->day_of_week] ?? 99,
            ]);

        if ($hours->isEmpty()) {
            return [];
        }

        $grouped = [];
        foreach ($hours as $wh) {
            $open = substr($wh->opening_time, 0, 5);
            $close = substr($wh->closing_time, 0, 5);
            $key = ($wh->is_day_off ? '1' : '0')."_{$open}_{$close}";

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'days' => [],
                    'opening_time' => $open,
                    'closing_time' => $close,
                    'is_day_off' => (bool) $wh->is_day_off,
                ];
            }

            $grouped[$key]['days'][] = $wh->day_of_week;
        }

        return array_values($grouped);
    }

    /**
     * Get human-readable summary of working hours.
     */
    public function getWorkingHoursSummaryAttribute(): string
    {
        $schedules = $this->getWorkingHoursSchedules();
        if (empty($schedules)) {
            return 'غير محدد';
        }

        $daysMap = [
            'sunday' => 'الأحد',
            'monday' => 'الإثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
            'saturday' => 'السبت',
        ];

        $summaries = [];
        foreach ($schedules as $schedule) {
            $days = array_map(fn ($d) => $daysMap[$d] ?? $d, $schedule['days'] ?? []);
            $daysStr = implode('، ', $days);

            if (! empty($schedule['is_day_off'])) {
                $summaries[] = "مغلق ({$daysStr})";

                continue;
            }

            $from = substr($schedule['opening_time'] ?? '', 0, 5);
            $to = substr($schedule['closing_time'] ?? '', 0, 5);
            if ($from && $to) {
                $summaries[] = "{$from} - {$to} ({$daysStr})";
            }
        }

        return empty($summaries) ? 'مغلق' : implode(' | ', $summaries);
    }

    /**
     * Check if branch is currently open across any configured shifts today.
     */
    public function isOpenNow(): bool
    {
        $now = now()->setTimezone('Asia/Riyadh');
        $today = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');

        $todayHours = $this->workingHours()->where('day_of_week', $today)->get();

        if ($todayHours->isEmpty()) {
            return false;
        }

        foreach ($todayHours as $hour) {
            if (! $hour->is_day_off && $currentTime >= $hour->opening_time && $currentTime <= $hour->closing_time) {
                return true;
            }
        }

        return false;
    }
}
