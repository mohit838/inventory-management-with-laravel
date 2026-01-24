<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_order_creation_deducts_stock()
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer '.$token];

        $cat = Category::create(['name' => 'C', 'slug' => 'c', 'active' => 1]);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Phone',
            'sku' => 'PHONE-001',
            'price' => 1000,
            'quantity' => 10,
            'active' => 1,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'payment_method' => 'cod', // Enum case-sensitive? Controller uses Enum(PaymentMethod::class) which expects 'cod' or 'online' values.
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ], $headers);

        $response->assertStatus(201);

        // Check Stock
        $this->assertDatabaseHas('products', ['id' => $product->id, 'quantity' => 8]);

        // Check Order
        $this->assertDatabaseHas('orders', ['total_amount' => 2000, 'customer_name' => 'John Doe']);
    }

    public function test_insufficient_stock_fails()
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer '.$token];

        $cat = Category::create(['name' => 'C', 'slug' => 'c', 'active' => 1]);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Laptop',
            'sku' => 'LAPTOP-001',
            'price' => 5000,
            'quantity' => 1,
            'active' => 1,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Jane',
            'payment_method' => 'online',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ], $headers);

        $response->assertStatus(400); // Service throws exception, Controller catches and returns 400
        $this->assertDatabaseHas('products', ['id' => $product->id, 'quantity' => 1]); // Unchanged
    }

    public function test_invoice_generation()
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer '.$token];

        $cat = Category::create(['name' => 'C', 'slug' => 'c', 'active' => 1]);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Book',
            'price' => 50,
            'quantity' => 100,
            'sku' => 'BK',
            'active' => 1,
        ]);

        // Create order via Service or API? API is easier.
        $createResp = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Book Reader',
            'payment_method' => 'cod',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ], $headers);
        $orderId = $createResp->json('data.id');

        $response = $this->getJson("/api/v1/orders/{$orderId}/invoice", $headers);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['invoice_number', 'items', 'total_amount']]);
    }
}
