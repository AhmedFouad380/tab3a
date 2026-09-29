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
            'working_hours' => $this->whenLoaded('workingHours', function () {
                return $this->workingHours->map(function ($wh) {
                    return [
                        'day_of_week' => $wh->day_of_week,
                        'opening_time' => substr($wh->opening_time, 0, 5),
                        'closing_time' => substr($wh->closing_time, 0, 5),
                        'is_day_off' => (bool) $wh->is_day_off,
                    ];
                });
            }),
        ];
    }
}
