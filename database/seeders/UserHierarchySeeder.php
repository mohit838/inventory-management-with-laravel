<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserHierarchySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create 2 Admins
        for ($i = 1; $i <= 2; $i++) {
            $admin = User::firstOrCreate(
                ['email' => "admin{$i}@example.com"],
                [
                    'name' => "Admin User {$i}",
                    'password' => Hash::make('password'),
                ]
            );
            $admin->assignRole('admin');
        }

        // 2. Create 3 Owners, each with a unique Tenant
        for ($i = 1; $i <= 3; $i++) {
            $tenant = Tenant::firstOrCreate(
                ['slug' => "tenant-{$i}"],
                ['name' => "Organization {$i}"]
            );

            $owner = User::firstOrCreate(
                ['email' => "owner{$i}@example.com"],
                [
                    'name' => "Owner User {$i}",
                    'password' => Hash::make('password'),
                    'tenant_id' => $tenant->id,
                ]
            );
            $owner->assignRole('owner');

            // 3. Create 2 Employees for each Owner, linked to same Tenant
            for ($j = 1; $j <= 2; $j++) {
                $employee = User::firstOrCreate(
                    ['email' => "employee{$i}-{$j}@example.com"],
                    [
                        'name' => "Employee {$i}-{$j}",
                        'password' => Hash::make('password'),
                        'tenant_id' => $tenant->id,
                    ]
                );
                $employee->assignRole('employee');
            }
        }
    }
}
