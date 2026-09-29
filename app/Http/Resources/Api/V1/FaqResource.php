<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language') === 'en' ? 'en' : 'ar';

        return [
            'id' => $this->id,
            'category' => $this->category,
            'question' => is_array($this->question) ? ($this->question[$locale] ?? $this->question['ar'] ?? '') : $this->question,
            'answer' => is_array($this->answer) ? ($this->answer[$locale] ?? $this->answer['ar'] ?? '') : $this->answer,
        ];
    }
}
