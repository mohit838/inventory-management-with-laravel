<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckInventoryLevels extends Command
{
    protected $signature = 'inventory:check-levels';

    protected $description = 'Check for low stock products and notify admins';

    public function handle()
    {
        $this->info('Checking inventory levels...');

        // 1. Get Low Stock Products using Same Logic as Analytics
        $lowStockProducts = \App\Models\Product::select('products.*')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.quantity', '>', 0)
            ->whereRaw('products.quantity <= COALESCE(products.low_stock_threshold, categories.low_stock_threshold, 10)')
            ->whereNull('products.deleted_at')
            ->with('category')
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low stock products found.');

            return;
        }

        // 2. Notify Admins
        $admins = \App\Models\User::whereIn('role', ['superadmin', 'admin'])->get();

        foreach ($lowStockProducts as $product) {
            $threshold = $product->low_stock_threshold ?? $product->category->low_stock_threshold ?? 10;

            // Optimization: In real app, check if notification already sent recently (Cache lock)
            // For now, valid MVP sends trigger on run.

            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LowStockAlert($product, $threshold));
            $this->info("Low Stock Alert Sent: {$product->name} ({$product->quantity} <= {$threshold})");
        }

        $this->info('Inventory check complete.');
    }
}
