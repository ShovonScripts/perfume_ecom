<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'combo_price',
        'image',
        'is_active',
        'sort_order'
    ];

    public function items()
    {
        return $this->hasMany(ComboItem::class);
    }

    public function getOriginalTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });
    }
}
