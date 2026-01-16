<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes, \App\Traits\ActiveScope;

    protected $fillable = ['category_id', 'subcategory_id', 'name', 'sku', 'description', 'price', 'quantity', 'active'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
