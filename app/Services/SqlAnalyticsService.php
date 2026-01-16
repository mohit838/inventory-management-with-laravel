<?php

namespace App\Services;

use App\Interfaces\AnalyticsServiceInterface;
use App\DTO\DashboardSummaryData;
use App\DTO\SalesChartData;
use App\Models\Product;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SqlAnalyticsService implements AnalyticsServiceInterface
{
    public function getSummary(int $userId, bool $includeRevenue = true): DashboardSummaryData
    {
        // Cache Key depends on includeRevenue
        $cacheKey = "dashboard_summary_{$userId}_" . ($includeRevenue ? 'rev' : 'norev');

        // Cache for 5 minutes to avoid heavy DB hits on refresh
        return Cache::remember($cacheKey, 300, function () use ($userId, $includeRevenue) {
            
            $totalProducts = Product::count();
            
            // Dynamic Low Stock: Product > Category > Global(10)
            // Using join to get category threshold.
            $lowStock = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('products.quantity', '>', 0) // Exclude out of stock
                ->whereRaw('products.quantity <= COALESCE(products.low_stock_threshold, categories.low_stock_threshold, 10)')
                ->whereNull('products.deleted_at') // Handle soft deletes
                ->count();

            $outStock = Product::where('quantity', '=', 0)->count();
            
            $totalOrders = Order::count();
            
            $totalRevenue = null;
            if ($includeRevenue) {
                $totalRevenue = Order::where('payment_status', PaymentStatus::PAID)
                    ->where('status', '!=', OrderStatus::CANCELLED)
                    ->sum('total_amount');
            }
            
            $pendingOrders = Order::where('status', OrderStatus::PENDING)->count();

            return new DashboardSummaryData(
                total_products: $totalProducts,
                low_stock_count: $lowStock,
                out_of_stock_count: $outStock,
                total_orders: $totalOrders,
                total_revenue: $totalRevenue ? (float) $totalRevenue : null,
                pending_orders_count: $pendingOrders
            );
        });
    }

    public function getSalesChart(int $userId, string $period = 'monthly'): SalesChartData
    {
        // Cache for 10 minutes
        return Cache::remember("sales_chart_{$userId}_{$period}", 600, function () use ($period) {
            
            $query = Order::where('payment_status', PaymentStatus::PAID)
                ->where('status', '!=', OrderStatus::CANCELLED);
            
            if ($period === 'monthly') {
                $start = Carbon::now()->startOfYear();
                $query->where('created_at', '>=', $start);
                
                // Group by Month
                // MySQL specific or generic?
                // Let's use generic Collection grouping implementation for portability or portable SQL.
                // SQLite/MySQL diff in date format. 
                // Using Collection approach is safer regarding SQL driver differences for this project.
                
                $orders = $query->get(['total_amount', 'created_at']);
                
                $grouped = $orders->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('M'); // Jan, Feb
                });
                
                // Ensure all months exist
                $labels = [];
                $values = [];
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                foreach ($months as $month) {
                    $labels[] = $month;
                    $values[] = isset($grouped[$month]) ? $grouped[$month]->sum('total_amount') : 0;
                }
                
                return new SalesChartData($labels, $values);
            }
            
            // Default empty
            return new SalesChartData([], []);
        });
    }
}
