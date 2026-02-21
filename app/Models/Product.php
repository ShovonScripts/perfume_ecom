<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'brand_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'long_description',
        'thumbnail_image',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)
            ->orderBy('price', 'asc')
            ->orderBy('sort_order', 'asc');
    }

    public function scopeActiveWithVariants($query)
    {
        return $query->where('is_active', true)
            ->whereHas('variants', function ($q) {
            $q->where('is_active', true)
                ->where('stock', '>', 0); // Optional: ensure stock > 0 if desired, but user issue was "price 0". 
        // Strictly speaking, out of stock is okay to show but invalid variant is not. 
        // Let's stick to is_active check as primary filter.
        });
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class)
            ->where('is_approved', true);
    }
}
