<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code','type','value',
        'min_subtotal','is_active',
        'starts_at','ends_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function isValid($subtotal)
    {
        if (!$this->is_active) return false;

        if ($this->min_subtotal && $subtotal < $this->min_subtotal)
            return false;

        if ($this->starts_at && now()->lt($this->starts_at))
            return false;

        if ($this->ends_at && now()->gt($this->ends_at))
            return false;

        return true;
    }

    public function calculateDiscount($subtotal)
    {
        if ($this->type === 'percent') {
            return ($subtotal * $this->value) / 100;
        }

        return min($this->value, $subtotal);
    }
}
