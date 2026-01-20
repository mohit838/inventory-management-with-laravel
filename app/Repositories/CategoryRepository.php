<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends EloquentBaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
