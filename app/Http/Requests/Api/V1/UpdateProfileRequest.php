<?php

namespace App\Http\Requests\Api\V1;

class UpdateProfileRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'preferred_locale' => 'nullable|in:ar,en',
            'fcm_token' => 'nullable|string',
        ];
    }
}
