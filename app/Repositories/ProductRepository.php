<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\Product;

class ProductRepository extends EloquentBaseRepository implements BaseRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
