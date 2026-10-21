<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KioskSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'kiosk_machine_id',
        'session_token',
        'client_ip',
        'status',
        'file_name',
        'file_path',
        'file_size_bytes',
        'detected_page_count',
        'copies_count',
        'total_sheets',
        'paper_size',
        'color_mode',
        'side_mode',
        'total_price',
        'order_id',
        'connected_at',
        'completed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'detected_page_count' => 'integer',
            'copies_count' => 'integer',
            'total_sheets' => 'integer',
            'connected_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function kiosk()
    {
        return $this->belongsTo(KioskMachine::class, 'kiosk_machine_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
