<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'key',
        'value',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
