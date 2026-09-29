<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language') === 'en' ? 'en' : 'ar';

        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => is_array($this->title) ? ($this->title[$locale] ?? $this->title['ar'] ?? '') : $this->title,
            'body' => is_array($this->body) ? ($this->body[$locale] ?? $this->body['ar'] ?? '') : $this->body,
            'data' => $this->data,
            'is_read' => (bool) $this->is_read,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
