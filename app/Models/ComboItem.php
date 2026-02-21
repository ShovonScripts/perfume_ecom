<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboItem extends Model
{
    protected $fillable = [
        'combo_id',
        'product_variant_id',
        'quantity'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class , 'product_variant_id');
    }
}
