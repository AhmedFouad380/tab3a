<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'original_file_name',
        'file_path',
        'file_extension',
        'file_size_bytes',
        'detected_page_count',
        'pages_to_print_count',
        'page_range_selection',
        'paper_size',
        'paper_type',
        'color_mode',
        'side_mode',
        'orientation',
        'copies_count',
        'finishing_option_id',
        'finishing_price',
        'total_sheets_needed',
        'unit_price_per_page',
        'total_item_price',
    ];

    protected function casts(): array
    {
        return [
            'finishing_price' => 'decimal:2',
            'unit_price_per_page' => 'decimal:2',
            'total_item_price' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function finishingOption()
    {
        return $this->belongsTo(FinishingOption::class);
    }
}
