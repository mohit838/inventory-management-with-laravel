<?php

namespace App\Interfaces;

interface AnalyticsServiceInterface
{
    public function getSummary(int $userId, bool $includeRevenue = true): array;

    public function getSalesChart(int $userId, string $period = 'monthly'): array;
}
