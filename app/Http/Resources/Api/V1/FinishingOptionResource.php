<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinishingOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language') === 'en' ? 'en' : 'ar';

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => is_array($this->name) ? ($this->name[$locale] ?? $this->name['ar'] ?? '') : $this->name,
            'description' => is_array($this->description) ? ($this->description[$locale] ?? $this->description['ar'] ?? '') : $this->description,
            'base_price' => (float) $this->base_price,
            'available_for_self_print' => (bool) $this->available_for_self_print,
            'available_for_pre_order' => (bool) $this->available_for_pre_order,
        ];
    }
}
