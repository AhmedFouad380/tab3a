<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishingOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'base_price',
        'available_for_self_print',
        'available_for_pre_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'base_price' => 'decimal:2',
            'available_for_self_print' => 'boolean',
            'available_for_pre_order' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
