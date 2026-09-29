<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_type' => $this->service_type,
            'paper_size' => $this->paper_size,
            'color_mode' => $this->color_mode,
            'side_mode' => $this->side_mode,
            'paper_type' => $this->paper_type,
            'price_per_page' => (float) $this->price_per_page,
        ];
    }
}
