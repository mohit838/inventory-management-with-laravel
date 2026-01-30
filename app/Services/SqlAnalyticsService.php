<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Services\RedisCacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SqlAnalyticsService
{
    public function __construct(protected RedisCacheService $cache) {}

    public function getSummary(int $userId, bool $includeRevenue = true): array
    {
        // Cache Key depends on includeRevenue
        $cacheKey = "dashboard_summary_{$userId}_".($includeRevenue ? 'rev' : 'norev');

        // Cache for 5 minutes to avoid heavy DB hits on refresh
        return $this->cache->remember($cacheKey, 300, function () use ($includeRevenue) {

            $totalProducts = Product::count();
            $totalCategories = \App\Models\Category::count();

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
            $totalValue = Product::selectRaw('SUM(quantity * price) as total_value')->value('total_value') ?? 0;

            $totalRevenue = null;
            if ($includeRevenue) {
                $totalRevenue = Order::where('payment_status', PaymentStatus::PAID)
                    ->where('status', '!=', OrderStatus::CANCELLED)
                    ->sum('total_amount');
            }

            $pendingOrders = Order::where('status', OrderStatus::PENDING)->count();

            $topCategoriesQuery = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();

            $grandTotalSold = $topCategoriesQuery->sum('total_sold');

            $topCategories = $topCategoriesQuery->map(function ($item) use ($grandTotalSold) {
                return [
                    'name' => $item->name,
                    'value' => (int) $item->total_sold,
                    'percentage' => $grandTotalSold > 0 ? round(($item->total_sold / $grandTotalSold) * 100, 1) : 0,
                ];
            })->toArray();

            return [
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories,
                'total_value' => (float) $totalValue,
                'total_orders' => $totalOrders,
                'total_revenue' => $includeRevenue ? (float) ($totalRevenue ?? 0) : null,
                'pending_orders' => $pendingOrders,
                'low_stock_alerts' => $lowStock,
                'out_of_stock_count' => $outStock,
                'top_categories' => $topCategories,
            ];
        });
    }

    public function getSalesChart(int $userId, string $period = 'monthly'): array
    {
        // Cache for 10 minutes
        return $this->cache->remember("sales_chart_{$userId}_{$period}", 600, function () use ($period) {

            if ($period === 'monthly') {
                $start = Carbon::now()->startOfYear();
                
                // Revenue Data
                $orders = Order::where('payment_status', PaymentStatus::PAID)
                    ->where('status', '!=', OrderStatus::CANCELLED)
                    ->where('created_at', '>=', $start)
                    ->get(['total_amount', 'created_at']);

                $revenueGrouped = $orders->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('M');
                });

                // Stock Variance Data (Quantity sold)
                $stockSales = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', '!=', OrderStatus::CANCELLED)
                    ->where('orders.created_at', '>=', $start)
                    ->select('order_items.quantity', 'orders.created_at')
                    ->get();

                $stockGrouped = $stockSales->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('M');
                });

                // Overall Order Counts
                $allOrders = Order::where('status', '!=', OrderStatus::CANCELLED)
                    ->where('created_at', '>=', $start)
                    ->get(['created_at']);

                $ordersGrouped = $allOrders->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('M');
                });

                $labels = [];
                $revenueData = [];
                $stockVarianceData = [];
                $ordersData = [];
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                foreach ($months as $month) {
                    $labels[] = $month;
                    $revenueData[] = isset($revenueGrouped[$month]) ? (float) $revenueGrouped[$month]->sum('total_amount') : 0;
                    // Stock variance is items leaving inventory (negative)
                    $stockVarianceData[] = isset($stockGrouped[$month]) ? (int) $stockGrouped[$month]->sum('quantity') * -1 : 0;
                    $ordersData[] = isset($ordersGrouped[$month]) ? $ordersGrouped[$month]->count() : 0;
                }

                return [
                    'labels' => $labels,
                    'revenue' => $revenueData,
                    'stock_variance' => $stockVarianceData,
                    'orders' => $ordersData,
                ];
            }

            return [
                'labels' => [],
                'revenue' => [],
                'stock_variance' => [],
            ];
        });
    }
}
