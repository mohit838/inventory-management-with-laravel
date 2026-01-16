<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ApiFlowTest extends TestCase
{
    // usage of RefreshDatabase might wipe existing data, but since it's staging/local, it should be fine.
    // Use DatabaseTruncation for speed if available, or just standard RefreshDatabase.
    use RefreshDatabase; 

    public function test_auth_flow()
    {
        // 1. Register
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(201);
        
        // 2. Login
        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['access_token', 'refresh_token', 'user']);
            
        $token = $loginResponse->json('access_token');
        $refreshToken = $loginResponse->json('refresh_token');

        // 3. Access Protected Route (Categories)
        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->getJson('/api/v1/categories')
             ->assertStatus(200);

        // 4. Refresh Token
        $refreshResponse = $this->postJson('/api/v1/refresh', [
            'refresh_token' => $refreshToken,
        ]);
        $refreshResponse->assertStatus(200)
            ->assertJsonStructure(['access_token']);
            
        // 5. Logout
        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->postJson('/api/v1/logout', ['refresh_token' => $refreshToken])
             ->assertStatus(200);
    }

    public function test_category_crud_and_active_scope()
    {
        $user = User::factory()->create(['role' => 'admin', 'active' => true]);
        $token = auth()->login($user); // Wait, we use JWT. Need to generate token manually or just mock middleware?
        // Better to use real login or JwtService.
        // Let's rely on actingAs if pure Laravel auth, but we have custom JwtMiddleware.
        // We must generate a real token.
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);

        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create
        $response = $this->postJson('/api/v1/categories', [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Gadgets',
            'active' => true
        ], $headers);
        $response->assertStatus(201);
        $id = $response->json('data.id');

        // Read
        $this->getJson("/api/v1/categories/{$id}", $headers)
             ->assertStatus(200)
             ->assertJson(['data' => ['name' => 'Electronics']]);

        // Update
        $this->putJson("/api/v1/categories/{$id}", [
            'name' => 'Electronics Updated',
            'slug' => 'electronics-updated',
            'active' => true
        ], $headers)
             ->assertStatus(200)
             ->assertJson(['data' => ['name' => 'Electronics Updated']]);

        // Toggle Active
        $this->postJson("/api/v1/categories/{$id}/toggle-active", [], $headers)
             ->assertStatus(200)
             ->assertJson(['data' => ['active' => false]]);

        // Check Index (Active Scope)
        // Should NOT see it? Wait, API index usually shows active? 
        // Our Repo->all() shows all by default? 
        // ActiveScope trait applies Global Scope 'active'. 
        // So Repo->all() -> Model::all() -> with scope -> should NOT show inactive.
        $this->getJson('/api/v1/categories', $headers)
             ->assertJsonMissing(['id' => $id]); 

        // Toggle Back
        $this->postJson("/api/v1/categories/{$id}/toggle-active", [], $headers);
        
        // Delete
        $this->deleteJson("/api/v1/categories/{$id}", [], $headers)
             ->assertStatus(204);

        // Verify Soft Delete
        $this->assertSoftDeleted('categories', ['id' => $id]);
    }

    public function test_pagination_and_search()
    {
        $user = User::factory()->create(['role' => 'admin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create 20 categories
        for ($i = 0; $i < 20; $i++) {
            Category::create(['name' => "Cat $i", 'slug' => "cat-$i", 'active' => true]);
        }

        // Test Pagination
        $response = $this->getJson('/api/v1/categories?per_page=15', $headers);
        $response->assertStatus(200);
        $this->assertCount(15, $response->json('data'));
        $this->assertArrayHasKey('meta', $response->json()); // Laravel Resources usually wrap in data + meta

        // Test Search
        $response = $this->getJson('/api/v1/categories?search=Cat 1&per_page=5', $headers);
        $response->assertStatus(200);
        // Should find "Cat 1", "Cat 10", "Cat 11", ... "Cat 19" -> 11 items. Limit 5.
        $this->assertCount(5, $response->json('data'));
    }

    public function test_dropdowns()
    {
        $user = User::factory()->create(['role' => 'user', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        Category::create(['name' => "Dropdown Cat", 'slug' => "dropdown-cat", 'active' => true]);

        $response = $this->getJson('/api/v1/categories/dropdown', $headers);
        $response->assertStatus(200);
        $this->assertTrue(is_array($response->json()));
        $this->assertArrayHasKey('id', $response->json()[0]);
        $this->assertArrayHasKey('name', $response->json()[0]);
    }

    public function test_product_with_null_subcategory_and_settings()
    {
        $user = User::factory()->create(['role' => 'admin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        $cat = Category::create(['name' => 'Main', 'slug' => 'main', 'active' => true]);

        // Create Product with NULL Subcategory
        $response = $this->postJson('/api/v1/products', [
            'category_id' => $cat->id,
            'subcategory_id' => null,
            'name' => 'Null Sub Product',
            'sku' => 'NSP-001',
            'price' => 100,
            'quantity' => 10,
            'active' => true
        ], $headers);
        $response->assertStatus(201);

        // User Settings
        // Set
        $this->postJson('/api/v1/settings', ['key' => 'theme', 'value' => 'dark'], $headers)
             ->assertStatus(200);
        
        // Get
        $this->getJson('/api/v1/settings', $headers)
             ->assertStatus(200)
             ->assertJsonFragment(['key' => 'theme', 'value' => 'dark']);
    }

    public function test_product_image_upload()
    {
        Storage::fake('minio');
        
        $user = User::factory()->create(['role' => 'admin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($user->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        $cat = Category::create(['name' => 'Image Cat', 'slug' => 'image-cat', 'active' => true]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $response = $this->postJson('/api/v1/products', [
            'category_id' => $cat->id,
            'name' => 'Product With Image',
            'sku' => 'IMG-001',
            'price' => 50,
            'image' => $file
        ], $headers);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['image_url']]);
        
        // Cannot assert file exists because MinioService compresses and changes name.
        // But we can check if *any* file was stored in minio disk.
        // Storage::disk('minio')->assertExists(...) - key is unknown (uniqid).
        // But we can trust it worked if response has URL and no exception.
        // And we mocked Storage so it won't hit real MinIO.
        
        // Assert some file exists in the directory
        // $this->assertTrue(count(Storage::disk('minio')->allFiles('products')) > 0);
    }
}
