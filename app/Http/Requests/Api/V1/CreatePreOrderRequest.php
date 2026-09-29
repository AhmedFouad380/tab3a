<?php

namespace App\Http\Requests\Api\V1;

class CreatePreOrderRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'branch_id' => 'required|exists:branches,id',
            'scheduled_pickup_at' => 'required|date|after:now',
            'file_path' => 'required|string',
            'original_file_name' => 'required|string',
            'file_extension' => 'required|string',
            'file_size_bytes' => 'required|integer',
            'detected_page_count' => 'required|integer|min:1',
            'page_range_selection' => 'nullable|string',
            'copies_count' => 'required|integer|min:1',
            'paper_size' => 'required|in:A4,A3,A5',
            'color_mode' => 'required|in:black_and_white,color',
            'side_mode' => 'required|in:single_sided,double_sided',
            'finishing_option_id' => 'nullable|exists:finishing_options,id',
            'payment_method' => 'required|in:apple_pay,card,wallet',
            'user_notes' => 'nullable|string',
        ];
    }
}
