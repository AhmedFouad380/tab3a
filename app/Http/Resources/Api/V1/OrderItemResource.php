<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language') === 'en' ? 'en' : 'ar';

        return [
            'id' => $this->id,
            'original_file_name' => $this->original_file_name,
            'file_url' => asset('storage/' . $this->file_path),
            'file_extension' => $this->file_extension,
            'detected_page_count' => (int) $this->detected_page_count,
            'pages_to_print_count' => (int) $this->pages_to_print_count,
            'page_range_selection' => $this->page_range_selection,
            'paper_size' => $this->paper_size,
            'color_mode' => $this->color_mode,
            'side_mode' => $this->side_mode,
            'copies_count' => (int) $this->copies_count,
            'total_sheets_needed' => (int) $this->total_sheets_needed,
            'finishing' => $this->finishingOption ? (is_array($this->finishingOption->name) ? ($this->finishingOption->name[$locale] ?? $this->finishingOption->name['ar'] ?? '') : $this->finishingOption->name) : null,
            'unit_price_per_page' => (float) $this->unit_price_per_page,
            'total_item_price' => (float) $this->total_item_price,
        ];
    }
}
