<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'latitude',
        'longitude',
        'phone',
        'whatsapp',
        'email',
        'is_active',
        'allows_pre_order',
        'daily_capacity',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'address' => 'array',
            'is_active' => 'boolean',
            'allows_pre_order' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function workingHours()
    {
        return $this->hasMany(BranchWorkingHour::class);
    }

    public function kiosks()
    {
        return $this->hasMany(KioskMachine::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
