<?php

namespace App\Interfaces;

use App\DTO\DashboardSummaryData;
use App\DTO\SalesChartData;

interface AnalyticsServiceInterface
{
    public function getSummary(int $userId, bool $includeRevenue = true): DashboardSummaryData;
    public function getSalesChart(int $userId, string $period = 'monthly'): SalesChartData;
}
