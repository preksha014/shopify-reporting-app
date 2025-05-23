<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'shopify_order_id',
        'name',
        'email',
        'created_at_shopify',
        'total_price',
        'currency_code',
    ];
}
