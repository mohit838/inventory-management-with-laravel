<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Artisan;

class AlertFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_threshold_inheritance_and_notification()
    {
        Notification::fake();
        
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        
        // 1. Setup Data
        // Case A: Category Threshold = 20, Product Qty = 15 (Should Alert)
        $catA = Category::create(['name' => 'Cat A', 'slug' => 'cat-a', 'active' => 1, 'low_stock_threshold' => 20]);
        $prodA = Product::factory()->create([
            'category_id' => $catA->id,
            'name' => 'Threshold Inherited',
            'quantity' => 15,
            'low_stock_threshold' => null // Inherit
        ]); // 15 <= 20 : ALERT

        // Case B: Category Threshold = 20, Product Threshold = 5, Product Qty = 15 (Should NOT Alert)
        $prodB = Product::factory()->create([
            'category_id' => $catA->id,
            'name' => 'Threshold Override',
            'quantity' => 15,
            'low_stock_threshold' => 5 // Override
        ]); // 15 > 5 : NO ALERT

        // Case C: Default Threshold (10), Product Qty = 8 (Should Alert)
        $catC = Category::create(['name' => 'Cat C', 'slug' => 'cat-c', 'active' => 1, 'low_stock_threshold' => null]);
        $prodC = Product::factory()->create([
            'category_id' => $catC->id,
            'name' => 'Default Threshold',
            'quantity' => 8,
            'low_stock_threshold' => null
        ]); // 8 <= 10 : ALERT

        // 2. Run Command
        Artisan::call('inventory:check-levels');

        // 3. Verify Notifications
        Notification::assertSentTo($admin, LowStockAlert::class, function ($notification) use ($prodA) {
            return $notification->product->id === $prodA->id;
        });

        Notification::assertSentTo($admin, LowStockAlert::class, function ($notification) use ($prodC) {
            return $notification->product->id === $prodC->id;
        });

        Notification::assertNotSentTo($admin, LowStockAlert::class, function ($notification) use ($prodB) {
            return $notification->product->id === $prodB->id;
        });
    }
}
