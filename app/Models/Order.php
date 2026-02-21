<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'name',
        'phone',
        'email',
        'city',
        'area',
        'address_line',
        'shipping_zone',
        'shipping_fee',
        'subtotal',
        'discount',
        'grand_total',
        'payment_method',
        'payment_status',
        'delivery_charge_prepaid',
        'delivery_trx_id',
        'status',
        'payment_sender_number',
        'payment_amount',
        'payment_transaction_id'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
