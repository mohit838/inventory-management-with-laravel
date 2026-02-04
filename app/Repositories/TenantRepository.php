<?php

namespace App\Repositories;

use App\Models\Tenant;

class TenantRepository
{
    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function findBySlug(string $slug): ?Tenant
    {
        return Tenant::where('slug', $slug)->first();
    }
}
