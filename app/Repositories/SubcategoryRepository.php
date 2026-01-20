<?php

namespace App\Repositories;

use App\Models\Subcategory;

class SubcategoryRepository extends EloquentBaseRepository
{
    public function __construct(Subcategory $model)
    {
        parent::__construct($model);
    }
}
