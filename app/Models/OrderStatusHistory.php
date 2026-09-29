<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'comment',
        'created_by_type',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'comment' => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
