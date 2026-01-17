<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends EloquentBaseRepository
{
    public function __construct(Category $model)
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
