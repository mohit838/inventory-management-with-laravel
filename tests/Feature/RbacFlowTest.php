<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;

class RbacFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_superadmin_has_all_access()
    {
        $super = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($super->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Should access categories create without explicit permission
        $this->postJson('/api/v1/categories', ['name' => 'SA Cat', 'slug' => 'sa-cat'], $headers)
             ->assertStatus(201);
    }

    public function test_employee_access_denied_without_permission()
    {
        $emp = User::factory()->create(['role' => 'employee', 'active' => true]);
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($emp->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Try View (Denied)
        $this->getJson('/api/v1/categories', $headers)->assertStatus(403);

        // Give Permission
        $perm = Permission::where('slug', 'categories.view')->first();
        $emp->permissions()->attach($perm);

        // Try View (Allowed)
        $this->getJson('/api/v1/categories', $headers)->assertStatus(200);

        // Try Create (Denied, only have View)
        $this->postJson('/api/v1/categories', ['name' => 'Emp Cat', 'slug' => 'emp-cat'], $headers)
             ->assertStatus(403);
    }

    public function test_admin_access_logic()
    {
        // Admin also needs permissions in DB? 
        // User said: "admin have access to give access... but if superadmin dont give this module permission then admin also dont have"
        // So Admin behaves like Employee but can Manage Users. 
        // Let's assume Admin needs permissions too (Subscribed logic).
        
        $admin = User::factory()->create(['role' => 'admin', 'active' => true]);
        // Give View permission
        $perm = Permission::where('slug', 'categories.view')->first();
        $admin->permissions()->attach($perm);

        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($admin->id);
        $headers = ['Authorization' => 'Bearer ' . $token];

        $this->getJson('/api/v1/categories', $headers)->assertStatus(200);
        $this->postJson('/api/v1/categories', ['name' => 'A Cat', 'slug' => 'a-cat'], $headers)->assertStatus(403);
    }
}
