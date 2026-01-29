<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\Category;

class CategoryRepository extends EloquentBaseRepository implements BaseRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
