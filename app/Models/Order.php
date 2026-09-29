<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'order_type',
        'branch_id',
        'kiosk_machine_id',
        'scheduled_pickup_at',
        'actual_pickup_at',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'printer_job_id',
        'failure_reason',
        'user_notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_pickup_at' => 'datetime',
            'actual_pickup_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function kiosk()
    {
        return $this->belongsTo(KioskMachine::class, 'kiosk_machine_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
