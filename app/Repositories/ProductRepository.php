<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends EloquentBaseRepository
{
    public function __construct(Product $model)
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
