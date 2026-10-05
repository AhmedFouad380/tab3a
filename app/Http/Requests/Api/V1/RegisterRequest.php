<?php

namespace App\Http\Requests\Api\V1;

class RegisterRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'phone_country_code' => 'nullable|string|max:10',
        ];
    }
}
