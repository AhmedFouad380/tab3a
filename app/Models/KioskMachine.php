<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KioskMachine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'machine_code',
        'qr_token',
        'nfc_tag_id',
        'name',
        'location_description',
        'latitude',
        'longitude',
        'status',
        'last_ping_at',
        'ip_address',
        'supports_color',
        'supports_duplex',
        'supported_paper_sizes',
        'paper_tray_a4_sheets',
        'paper_tray_a3_sheets',
        'is_paper_low',
        'is_paper_empty',
        'black_toner_level',
        'cyan_toner_level',
        'magenta_toner_level',
        'yellow_toner_level',
        'is_toner_low',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'location_description' => 'array',
            'supported_paper_sizes' => 'array',
            'supports_color' => 'boolean',
            'supports_duplex' => 'boolean',
            'is_paper_low' => 'boolean',
            'is_paper_empty' => 'boolean',
            'is_toner_low' => 'boolean',
            'last_ping_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function healthChecks()
    {
        return $this->hasMany(KioskHealthCheck::class);
    }

    public function alerts()
    {
        return $this->hasMany(KioskMaintenanceAlert::class);
    }
}
