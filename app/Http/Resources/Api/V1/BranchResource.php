<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language') === 'en' ? 'en' : 'ar';

        return [
            'id' => $this->id,
            'name' => is_array($this->name) ? ($this->name[$locale] ?? $this->name['ar'] ?? '') : $this->name,
            'address' => is_array($this->address) ? ($this->address[$locale] ?? $this->address['ar'] ?? '') : $this->address,
            'city' => $this->city,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'distance_km' => $this->distance_km ?? null,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'allows_pre_order' => (bool) $this->allows_pre_order,
            'is_open' => $this->isOpenNow(),
            'working_hours_summary' => $this->working_hours_summary,
            'working_hours_schedules' => $this->getWorkingHoursSchedules(),
            'working_hours' => $this->whenLoaded('workingHours', function () {
                $allDays = [
                    'sunday' => 'الأحد',
                    'monday' => 'الإثنين',
                    'tuesday' => 'الثلاثاء',
                    'wednesday' => 'الأربعاء',
                    'thursday' => 'الخميس',
                    'friday' => 'الجمعة',
                    'saturday' => 'السبت',
                ];

                $groupedByDay = $this->workingHours->groupBy('day_of_week');

                $result = [];
                foreach ($allDays as $dayKey => $dayLabel) {
                    $dayHours = $groupedByDay->get($dayKey);

                    if (! $dayHours || $dayHours->isEmpty()) {
                        $result[] = [
                            'day_of_week' => $dayKey,
                            'opening_time' => '00:00',
                            'closing_time' => '00:00',
                            'is_day_off' => true,
                        ];

                        continue;
                    }

                    $sortedHours = $dayHours->sortBy('opening_time');

                    foreach ($sortedHours as $wh) {
                        $result[] = [
                            'day_of_week' => $wh->day_of_week,
                            'opening_time' => substr($wh->opening_time, 0, 5),
                            'closing_time' => substr($wh->closing_time, 0, 5),
                            'is_day_off' => (bool) $wh->is_day_off,
                        ];
                    }
                }

                return $result;
            }),
        ];
    }
}
