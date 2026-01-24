<?php

namespace App\Interfaces;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function createOrderWithItems(array $orderData, array $itemsData);
}
