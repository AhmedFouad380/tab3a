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
        'max_file_size_mb',
        'min_order_price',
        'max_sheets_per_job',
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
            'max_file_size_mb' => 'integer',
            'min_order_price' => 'decimal:2',
            'max_sheets_per_job' => 'integer',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sessions()
    {
        return $this->hasMany(KioskSession::class);
    }

    public function activeSession()
    {
        return $this->hasOne(KioskSession::class)
            ->whereIn('status', ['waiting_scan', 'connected', 'file_uploaded', 'awaiting_payment', 'printing'])
            ->latestOfMany();
    }

    /**
     * Get effective maximum file size in MB (machine-specific or system default)
     */
    public function getEffectiveMaxFileSizeMb(): int
    {
        if ($this->max_file_size_mb && $this->max_file_size_mb > 0) {
            return (int) $this->max_file_size_mb;
        }

        $globalSetting = SystemSetting::where('key', 'instant_print_max_file_size')->first();

        return (int) ($globalSetting?->value['ar'] ?? 30);
    }

    /**
     * Get effective minimum order price in SAR (machine-specific or system default)
     */
    public function getEffectiveMinOrderPrice(): float
    {
        if ($this->min_order_price && $this->min_order_price > 0) {
            return (float) $this->min_order_price;
        }

        $globalSetting = SystemSetting::where('key', 'instant_print_min_order_price')->first();

        return (float) ($globalSetting?->value['ar'] ?? 2.00);
    }

    /**
     * Get effective maximum sheets per job (machine-specific or system default)
     */
    public function getEffectiveMaxSheets(): int
    {
        if ($this->max_sheets_per_job && $this->max_sheets_per_job > 0) {
            return (int) $this->max_sheets_per_job;
        }

        $globalSetting = SystemSetting::where('key', 'instant_print_max_sheets')->first();

        return (int) ($globalSetting?->value['ar'] ?? 100);
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
