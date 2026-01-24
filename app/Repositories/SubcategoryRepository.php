<?php

namespace App\Repositories;

use App\Interfaces\SubcategoryRepositoryInterface;
use App\Models\Subcategory;

class SubcategoryRepository extends EloquentBaseRepository implements SubcategoryRepositoryInterface
{
    public function __construct(Subcategory $model)
    {
        parent::__construct($model);
    }
}
