<?php

namespace App\Http\Requests\Api\V1;

class PriceCalculationRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'service_type' => 'required|in:self_printing,pre_order',
            'detected_page_count' => 'required|integer|min:1',
            'page_range_selection' => 'nullable|string',
            'copies_count' => 'required|integer|min:1',
            'paper_size' => 'required|in:A4,A3,A5',
            'color_mode' => 'required|in:black_and_white,color',
            'side_mode' => 'required|in:single_sided,double_sided',
            'paper_type' => 'nullable|string',
            'finishing_option_id' => 'nullable|exists:finishing_options,id',
        ];
    }
}
