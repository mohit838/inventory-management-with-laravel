<?php

namespace App\Repositories;

use App\Models\Subcategory;

class SubcategoryRepository extends EloquentBaseRepository
{
    public function __construct(Subcategory $model)
    {
        parent::__construct($model);
    }

    public function toggleActive($id)
    {
        $item = $this->findWithInactive($id);
        $item->active = ! $item->active;
        $item->save();
        
        return $item;
    }
}
