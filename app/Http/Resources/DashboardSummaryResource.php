<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        // $this->resource is the array returned by AnalyticsService
        return [
            'total_products' => $this->resource['total_products'] ?? 0,
            'low_stock_count' => $this->resource['low_stock_count'] ?? 0,
            'out_of_stock_count' => $this->resource['out_of_stock_count'] ?? 0,
            'total_orders' => $this->resource['total_orders'] ?? 0,
            'total_revenue' => $this->when(array_key_exists('total_revenue', $this->resource), $this->resource['total_revenue']),
            'pending_orders_count' => $this->resource['pending_orders_count'] ?? 0,
            'top_categories' => $this->resource['top_categories'] ?? [],
        ];
    }
}
