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
            'low_stock_alerts' => $this->resource['low_stock_alerts'] ?? 0,
            'total_orders' => $this->resource['total_orders'] ?? 0,
            'revenue' => $this->when(isset($this->resource['revenue']), $this->resource['revenue'] ?? 0.0),
            'top_categories' => $this->resource['top_categories'] ?? [],
        ];
    }
}
