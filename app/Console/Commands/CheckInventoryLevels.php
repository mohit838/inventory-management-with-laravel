<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckInventoryLevels extends Command
{
    protected $signature = 'inventory:check-levels';

    protected $description = 'Check for low stock products and notify admins';

    public function handle()
    {
        $this->info('Checking inventory levels...');

        // 1. Get Low Stock Products using Same Logic as Analytics
        $lowStockProducts = Product::select('products.*')
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
        $admins = User::whereIn('role', ['superadmin', 'admin'])->get();

        foreach ($lowStockProducts as $product) {
            $threshold = $product->low_stock_threshold ?? $product->category->low_stock_threshold ?? 10;

            // Optimization: In real app, check if notification already sent recently (Cache lock)
            // For now, valid MVP sends trigger on run.

            Notification::send($admins, new LowStockAlert($product, $threshold));
            $this->info("Low Stock Alert Sent: {$product->name} ({$product->quantity} <= {$threshold})");
        }

        $this->info('Inventory check complete.');
    }
}
