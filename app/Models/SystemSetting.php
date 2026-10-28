<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'display_name',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
            'display_name' => 'array',
        ];
    }

    /**
     * Ensure localized value attributes are always flat strings/scalars, not arrays.
     */
    public function getValueAttribute($value): mixed
    {
        $decoded = is_string($value) ? json_decode($value, true) : $value;

        if (is_array($decoded)) {
            if (isset($decoded['ar']) && is_array($decoded['ar'])) {
                $decoded['ar'] = reset($decoded['ar']);
            }
            if (isset($decoded['en']) && is_array($decoded['en'])) {
                $decoded['en'] = reset($decoded['en']);
            }
        }

        return $decoded;
    }

    /**
     * Clean array values before saving to prevent nested arrays from FileUpload/Livewire.
     */
    public function setValueAttribute(mixed $value): void
    {
        if (is_array($value)) {
            if (isset($value['ar']) && is_array($value['ar'])) {
                $value['ar'] = reset($value['ar']);
            }
            if (isset($value['en']) && is_array($value['en'])) {
                $value['en'] = reset($value['en']);
            }
            $this->attributes['value'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($value)) {
            $this->attributes['value'] = $value;
        } else {
            $this->attributes['value'] = json_encode($value);
        }
    }

    /**
     * Safely retrieve a setting value as a string.
     */
    public static function getSetting(string $key, string $locale = 'ar', mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        $val = $setting->value;

        if (is_array($val)) {
            $res = $val[$locale] ?? $val['ar'] ?? reset($val);
            while (is_array($res)) {
                $res = reset($res);
            }

            return $res !== null && $res !== '' ? $res : $default;
        }

        return $val !== null && $val !== '' ? $val : $default;
    }
}
