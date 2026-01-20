<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDropdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_dropdown_returns_correct_data()
    {
        // Setup user and token
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Setup category and products
        $cat = Category::create(['name' => 'Test Cat', 'slug' => 'test-cat', 'active' => true]);
        
        Product::create([
            'category_id' => $cat->id,
            'name' => 'Active Product 1',
            'sku' => 'SKU-001',
            'price' => 100,
            'quantity' => 10,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $cat->id,
            'name' => 'Active Product 2',
            'sku' => 'SKU-002',
            'price' => 200,
            'quantity' => 20,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $cat->id,
            'name' => 'Inactive Product',
            'sku' => 'SKU-003',
            'price' => 300,
            'quantity' => 30,
            'active' => false,
        ]);

        // Call the dropdown endpoint
        $response = $this->getJson('/api/v1/products/dropdown', $headers);

        // Verify response
        $response->assertStatus(200);
        $data = $response->json('data');

        // Should only show active products (2)
        $this->assertCount(2, $data);
        
        $this->assertEquals('Active Product 1', $data[0]['name']);
        $this->assertEquals('SKU-001', $data[0]['sku']);
        
        $this->assertEquals('Active Product 2', $data[1]['name']);
        $this->assertEquals('SKU-002', $data[1]['sku']);

        // Verify inactive product is NOT in the list
        foreach ($data as $item) {
            $this->assertNotEquals('Inactive Product', $item['name']);
        }
    }

    public function test_product_dropdown_is_protected_by_auth()
    {
        $response = $this->getJson('/api/v1/products/dropdown');
        $response->assertStatus(401);
    }
}
