<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language') === 'en' ? 'en' : 'ar';

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_type' => $this->order_type,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'total_amount' => (float) $this->total_amount,
            'scheduled_pickup_at' => $this->scheduled_pickup_at?->toIso8601String(),
            'actual_pickup_at' => $this->actual_pickup_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'kiosk' => new KioskResource($this->whenLoaded('kiosk')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'status_history' => $this->whenLoaded('statusHistories', function () use ($locale) {
                return $this->statusHistories->map(function ($h) use ($locale) {
                    return [
                        'status' => $h->status,
                        'comment' => is_array($h->comment) ? ($h->comment[$locale] ?? $h->comment['ar'] ?? '') : $h->comment,
                        'created_at' => $h->created_at->toIso8601String(),
                    ];
                });
            }),
        ];
    }
}
