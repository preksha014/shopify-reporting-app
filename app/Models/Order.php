<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shopify_order_id',
        'customer_id',
        'shipping_address_id',
        'billing_address_id',
        'phone',
        'email',
        'custom_attributes',
        'tags',
        'note',
    ];

    protected $casts = [
        'custom_attributes' => 'array',
        'tags' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function orderAttributes()
    {
        return $this->hasMany(OrderAttribute::class);
    }
}
