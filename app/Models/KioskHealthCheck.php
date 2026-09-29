<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KioskHealthCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'kiosk_machine_id',
        'order_id',
        'user_id',
        'connection_passed',
        'paper_passed',
        'toner_passed',
        'settings_passed',
        'is_ready_to_print',
        'failed_step',
        'error_message',
        'telemetry_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'connection_passed' => 'boolean',
            'paper_passed' => 'boolean',
            'toner_passed' => 'boolean',
            'settings_passed' => 'boolean',
            'is_ready_to_print' => 'boolean',
            'error_message' => 'array',
            'telemetry_snapshot' => 'array',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
