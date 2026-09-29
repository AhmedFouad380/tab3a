<?php

namespace App\Http\Requests\Api\V1;

class CompleteProfileRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'preferred_locale' => 'nullable|in:ar,en',
        ];
    }
}
