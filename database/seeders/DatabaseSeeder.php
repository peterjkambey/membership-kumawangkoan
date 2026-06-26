<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        if (User::where('email', 'superadmin@anyflow.site')->doesntExist()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@anyflow.site',
                'password' => '1234qwE!', // "hashed" cast will bcrypt automatically
            ])->assignRole('super-admin');
        }

        $regions = ['Wilayah 1', 'Wilayah 2', 'Wilayah 3', 'Wilayah 4', 'Wilayah 5', 'Wilayah 6', 'Wilayah 7'];
        foreach ($regions as $name) {
            \App\Models\Region::firstOrCreate(['name' => $name]);
        }
    }
}
