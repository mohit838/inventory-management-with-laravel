<?php

namespace App\DTO;

class DashboardSummaryData
{
    public function __construct(
        public int $total_products,
        public int $low_stock_count,
        public int $out_of_stock_count,
        public int $total_orders,
        public ?float $total_revenue,
        public int $pending_orders_count
    ) {}
}
