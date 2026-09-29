<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'phone_country_code' => $this->phone_country_code,
            'email' => $this->email,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'preferred_locale' => $this->preferred_locale,
            'wallet_balance' => (float) $this->wallet_balance,
            'status' => $this->status,
            'total_orders' => $this->whenCounted('orders', $this->orders_count),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
