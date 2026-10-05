<?php

namespace App\Http\Requests\Api\V1;

class LoginRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|string',
            'phone_country_code' => 'nullable|string|max:10',
        ];
    }
}
