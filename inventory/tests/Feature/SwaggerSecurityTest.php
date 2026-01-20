<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SwaggerSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that accessing Swagger UI without credentials fails.
     */
    public function test_swagger_ui_requires_basic_auth()
    {
        $response = $this->get('/api/documentation');

        $response->assertStatus(401);
        $response->assertHeader('WWW-Authenticate', 'Basic realm="API Documentation"');
    }

    /**
     * Test that accessing Swagger UI with invalid credentials fails.
     */
    public function test_swagger_ui_fails_with_invalid_credentials()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode('wrong@email.com:wrongpassword'),
        ])->get('/api/documentation');

        $response->assertStatus(401);
    }

    /**
     * Test that a non-superadmin (e.g., regular user/admin) cannot access Swagger UI even with valid credentials.
     */
    public function test_non_superadmin_cannot_access_swagger_ui()
    {
        $user = User::factory()->create([
            'role' => 'admin', // or 'user', 'employee'
            'password' => Hash::make('password'),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode($user->email . ':password'),
        ])->get('/api/documentation');

        $response->assertStatus(401);
    }

    /**
     * Test that a superadmin can access Swagger UI with valid credentials.
     */
    public function test_superadmin_can_access_swagger_ui()
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
            'password' => Hash::make('password'),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode($superAdmin->email . ':password'),
        ])->get('/api/documentation');

        $response->assertStatus(200);
    }
}
