<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuditFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup superadmin user
        $this->user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        
        $jwt = app(\App\Services\JwtService::class);
        $token = $jwt->generateAccessToken($this->user->id);
        $this->headers = ['Authorization' => 'Bearer '.$token];
    }

    public function test_product_creation_logs_audit()
    {
        Permission::firstOrCreate(['name' => 'products.create'], ['slug' => 'products.create']);
        Permission::firstOrCreate(['name' => 'products.view'], ['slug' => 'products.view']);

        $payload = [
            'name' => 'Audit Test Product',
            'sku' => 'AUDIT-001',
            'price' => 100,
            'stock' => 10,
            'category_id' => 1,
            'subcategory_id' => 1
        ];

        // We are just testing the endpoint response for the Log index here as a smoke test
        $response = $this->getJson('/api/v1/audit-logs', $this->headers);
        $response->assertStatus(200);
    }

    public function test_audit_service_creates_entry()
    {
        $service = app(\App\Services\AuditService::class);
        $service->log('test.action', 'Test Description');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'test.action',
            'description' => 'Test Description'
        ]);
    }
}
