<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $this->resource is the array returned by AnalyticsService
        return [
            'total_products' => $this->resource['total_products'] ?? 0,
            'total_categories' => $this->resource['total_categories'] ?? 0,
            'total_value' => $this->resource['total_value'] ?? 0,
            'total_orders' => $this->resource['total_orders'] ?? 0,
            'total_revenue' => $this->when(array_key_exists('total_revenue', $this->resource), $this->resource['total_revenue']),
            'pending_orders' => $this->resource['pending_orders'] ?? 0,
            'low_stock_alerts' => $this->resource['low_stock_alerts'] ?? 0,
            'out_of_stock_count' => $this->resource['out_of_stock_count'] ?? 0,
            'top_categories' => $this->resource['top_categories'] ?? [],
            // Keep old keys for BC
            'low_stock_count' => $this->resource['low_stock_alerts'] ?? 0,
            'pending_orders_count' => $this->resource['pending_orders'] ?? 0,
        ];
    }
}
