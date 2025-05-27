<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone', 'email', 'city', 'shipping_address', 'billing_address',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
