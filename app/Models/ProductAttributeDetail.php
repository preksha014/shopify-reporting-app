<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttributeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_attribute_id',
        'key',
        'value',
    ];

    public function productAttribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }
}
