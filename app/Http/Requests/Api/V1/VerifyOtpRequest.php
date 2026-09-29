<?php

namespace App\Http\Requests\Api\V1;

class VerifyOtpRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|string',
            'otp_code' => 'required|string',
            'fcm_token' => 'nullable|string',
        ];
    }
}
