<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

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
        $id = $response->json('id');

        // Read
        $this->getJson("/api/v1/categories/{$id}", $headers)
             ->assertStatus(200)
             ->assertJson(['name' => 'Electronics']);

        // Update
        $this->putJson("/api/v1/categories/{$id}", [
            'name' => 'Electronics Updated',
            'slug' => 'electronics-updated',
            'active' => true
        ], $headers)->assertStatus(200);

        // Toggle Active
        $this->postJson("/api/v1/categories/{$id}/toggle-active", [], $headers)
             ->assertStatus(200)
             ->assertJson(['active' => false]);

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
}
