<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\ActiveScope, HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'sku',
        'description',
        'image_url',
        'price',
        'quantity',
        'active',
        'low_stock_threshold',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
