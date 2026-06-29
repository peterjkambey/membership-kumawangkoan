<?php

namespace Tests\Helpers;

use App\Models\Region;
use App\Models\User;
use Spatie\Permission\Models\Role;

trait CreatesUsers
{
    protected function createSuperAdmin(): User
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
        ]);
        $user->assignRole('super-admin');

        return $user;
    }

    protected function createRegionAdmin(?Region $region = null): User
    {
        if (!$region) {
            $region = Region::factory()->create();
        }

        $user = User::factory()->create([
            'name' => 'Admin Wilayah',
            'email' => 'admin.wilayah@test.com',
            'region_id' => $region->id,
        ]);
        $user->assignRole('region-admin');

        return $user;
    }

    protected function createFinanceAdmin(): User
    {
        $user = User::factory()->create([
            'name' => 'Finance Admin',
            'email' => 'finance@test.com',
        ]);
        $user->assignRole('finance-admin');

        return $user;
    }

    protected function createViewer(): User
    {
        $user = User::factory()->create([
            'name' => 'Viewer',
            'email' => 'viewer@test.com',
        ]);
        $user->assignRole('viewer');

        return $user;
    }
}
