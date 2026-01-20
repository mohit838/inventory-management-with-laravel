<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use \App\Traits\ActiveScope, HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'active', 'low_stock_threshold'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
