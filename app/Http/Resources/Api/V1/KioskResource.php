<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KioskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language') === 'en' ? 'en' : 'ar';

        return [
            'id' => $this->id,
            'machine_code' => $this->machine_code,
            'name' => is_array($this->name) ? ($this->name[$locale] ?? $this->name['ar'] ?? '') : $this->name,
            'location' => is_array($this->location_description) ? ($this->location_description[$locale] ?? $this->location_description['ar'] ?? '') : $this->location_description,
            'status' => $this->status,
            'supports_color' => (bool) $this->supports_color,
            'supports_duplex' => (bool) $this->supports_duplex,
            'paper_tray_a4_sheets' => (int) $this->paper_tray_a4_sheets,
            'paper_tray_a3_sheets' => (int) $this->paper_tray_a3_sheets,
            'black_toner_level' => (int) $this->black_toner_level,
            'cyan_toner_level' => (int) $this->cyan_toner_level,
            'magenta_toner_level' => (int) $this->magenta_toner_level,
            'yellow_toner_level' => (int) $this->yellow_toner_level,
            'branch' => new BranchResource($this->whenLoaded('branch')),
        ];
    }
}
