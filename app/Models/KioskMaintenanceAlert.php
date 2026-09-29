<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KioskMaintenanceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'kiosk_machine_id',
        'alert_type',
        'severity',
        'message',
        'is_resolved',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'message' => 'array',
            'is_resolved' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function kiosk()
    {
        return $this->belongsTo(KioskMachine::class, 'kiosk_machine_id');
    }
}
