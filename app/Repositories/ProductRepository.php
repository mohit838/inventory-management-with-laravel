<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends EloquentBaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
