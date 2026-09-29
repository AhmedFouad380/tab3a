<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchWorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'day_of_week',
        'opening_time',
        'closing_time',
        'is_day_off',
    ];

    protected function casts(): array
    {
        return [
            'is_day_off' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
