<?php

namespace App\Http\Requests\Api\V1;

class CreateSelfPrintOrderRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'session_token' => 'nullable|string|exists:kiosk_sessions,session_token',
            'payment_method' => 'required|in:apple_pay,card,wallet',
            'is_teacher' => 'nullable|boolean',
            'page_range_selection' => 'nullable|string',

            // These fields are only required if session_token is not provided
            'kiosk_machine_id' => 'required_without:session_token|exists:kiosk_machines,id',
            'file_path' => 'required_without:session_token|string',
            'original_file_name' => 'required_without:session_token|string',
            'file_extension' => 'nullable|string',
            'file_size_bytes' => 'nullable|integer',
            'detected_page_count' => 'required_without:session_token|integer|min:1',
            'copies_count' => 'nullable|integer|min:1',
            'paper_size' => 'nullable|in:A4,A3,A5',
            'color_mode' => 'nullable|in:black_and_white,color',
            'side_mode' => 'nullable|in:single_sided,double_sided',
        ];
    }
}
