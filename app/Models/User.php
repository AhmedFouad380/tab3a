<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone_country_code',
        'phone',
        'email',
        'phone_verified_at',
        'avatar',
        'preferred_locale',
        'fcm_token',
        'wallet_balance',
        'status',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'wallet_balance' => 'decimal:2',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function contactMessages()
    {
        return $this->hasMany(ContactMessage::class);
    }
}
