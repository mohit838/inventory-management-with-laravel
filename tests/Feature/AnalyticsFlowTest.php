<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;

class AnalyticsFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_dashboard_summary_metrics()
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 1. Setup Data
        // Products
        $cat = Category::create(['name' => 'C', 'slug' => 'c', 'active' => 1]);
        Product::factory()->count(5)->create(['quantity' => 20, 'category_id' => $cat->id, 'active' => 1]); // Normal
        Product::factory()->count(2)->create(['quantity' => 5, 'category_id' => $cat->id, 'active' => 1]);  // Low Stock
        Product::factory()->count(1)->create(['quantity' => 0, 'category_id' => $cat->id, 'active' => 1]);  // Out Stock
        // Total Products = 8.
        
        // Orders
        // 1 Completed Paid Order ($100)
        Order::create([
            'user_id' => $user->id,
            'customer_name' => 'Cust 1', 
            'total_amount' => 100, 
            'status' => OrderStatus::COMPLETED, 
            'payment_status' => PaymentStatus::PAID, 
            'payment_method' => \App\Enums\PaymentMethod::ONLINE
        ]);
        
        // 1 Pending Order ($50)
        Order::create([
            'user_id' => $user->id,
            'customer_name' => 'Cust 2', 
            'total_amount' => 50, 
            'status' => OrderStatus::PENDING, 
            'payment_status' => PaymentStatus::PENDING, 
            'payment_method' => \App\Enums\PaymentMethod::COD
        ]);

        // 2. Call API
        $response = $this->getJson('/api/v1/dashboard/summary', $headers);

        // 3. Verify
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(8, $data['total_products']);
        $this->assertEquals(2, $data['low_stock_count']);
        $this->assertEquals(1, $data['out_of_stock_count']);
        $this->assertEquals(2, $data['total_orders']);
        $this->assertEquals(100.0, $data['total_revenue']); // Pending/Unpaid excluded from Revenue usually?
        $this->assertEquals(1, $data['pending_orders_count']);
    }

    public function test_sales_chart_structure()
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        $response = $this->getJson('/api/v1/dashboard/chart?period=monthly', $headers);
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['labels', 'values']]);
        
        $labels = $response->json('data.labels');
        $this->assertCount(12, $labels); // Jan - Dec
    }

    public function test_restricted_user_cannot_see_revenue()
    {
        // 1. Employee with Dashboard View but NO Revenue View
        $emp = User::factory()->create(['role' => 'employee', 'active' => true]);
        
        // Give View Dashboard Perm
        $perm = \App\Models\Permission::where('slug', 'dashboard.view')->first();
        $emp->permissions()->attach($perm);
        
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($emp->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 2. Metrics (Should be Null revenue)
        $response = $this->getJson('/api/v1/dashboard/summary', $headers);
        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertNull($data['total_revenue']);
        $this->assertNotNull($data['total_products']); // Should still see counts
        
        // 3. Chart (Should be 403)
        $this->getJson('/api/v1/dashboard/chart', $headers)->assertStatus(403);
    }
}
