<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\Subcategory;

class SubcategoryRepository extends EloquentBaseRepository implements BaseRepositoryInterface
{
    public function __construct(Subcategory $model)
    {
        parent::__construct($model);
    }
}
