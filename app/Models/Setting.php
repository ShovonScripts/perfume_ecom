<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key','value'];

    /**
     * Get a setting value — cached for 1 hour.
     */
    public static function get($key, $default = null)
    {
        return Cache::remember('settings.' . $key, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value and bust its cache.
     */
    public static function set($key, $value)
    {
        Cache::forget('settings.' . $key);

        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
