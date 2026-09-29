<?php

namespace App\Http\Requests\Api\V1;

class VerifyKioskRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'qr_token' => 'nullable|string',
            'nfc_tag_id' => 'nullable|string',
            'machine_code' => 'nullable|string',
            'total_sheets_needed' => 'required|integer|min:1',
            'paper_size' => 'required|in:A4,A3,A5',
            'color_mode' => 'required|in:black_and_white,color',
            'side_mode' => 'required|in:single_sided,double_sided',
        ];
    }
}
