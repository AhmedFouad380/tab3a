<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type',
        'paper_size',
        'color_mode',
        'side_mode',
        'paper_type',
        'price_per_page',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_page' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
