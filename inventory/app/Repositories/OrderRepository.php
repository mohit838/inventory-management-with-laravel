<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends EloquentBaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function createOrderWithItems(array $orderData, array $itemsData)
    {
        // 1. Create Order
        $order = $this->create($orderData);

        // 2. Create Items
        foreach ($itemsData as $item) {
            $order->items()->create($item);
        }

        return $order->load('items');
    }
}
