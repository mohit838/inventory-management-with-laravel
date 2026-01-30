<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\Order;
use Exception;

class OrderRepository extends EloquentBaseRepository implements BaseRepositoryInterface
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

    public function findWithItems($id)
    {
        return $this->model->with(['items.product'])->findOrFail($id);
    }

    protected function applyFilters($query, array $filters)
    {
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->whereIn('status', [\App\Enums\OrderStatus::PENDING, \App\Enums\OrderStatus::PROCESSING]);
            } elseif ($filters['status'] === 'archived') {
                $query->whereIn('status', [\App\Enums\OrderStatus::COMPLETED, \App\Enums\OrderStatus::CANCELLED]);
            }
        }

        return $query;
    }
}
