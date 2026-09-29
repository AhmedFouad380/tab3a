<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaticPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language') === 'en' ? 'en' : 'ar';

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => is_array($this->title) ? ($this->title[$locale] ?? $this->title['ar'] ?? '') : $this->title,
            'content' => is_array($this->content) ? ($this->content[$locale] ?? $this->content['ar'] ?? '') : $this->content,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
