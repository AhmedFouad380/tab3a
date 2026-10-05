<?php

namespace App\Http\Requests\Api\V1;

class ResendOtpRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|string',
            'type' => 'nullable|in:login,register',
            'phone_country_code' => 'nullable|string|max:10',
        ];
    }
}
