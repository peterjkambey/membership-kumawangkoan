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
            BenefitSeeder::class,
        ]);

        if (User::where('email', 'superadmin@anyflow.site')->doesntExist()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@anyflow.site',
                'password' => '1234qwE!', // "hashed" cast will bcrypt automatically
            ])->assignRole('super-admin');
        }

        $regions = [
            ['name' => 'Wilayah 1 - Tompola', 'code' => 'TMP'],
            ['name' => 'Wilayah 2 - Mawale', 'code' => 'MWL'],
            ['name' => 'Wilayah 3 - Tombara\'an', 'code' => 'TMB'],
            ['name' => 'Wilayah 4 - Lewetan', 'code' => 'LWT'],
            ['name' => 'Wilayah 5 - Wawona', 'code' => 'WWN'],
            ['name' => 'Wilayah 6 - Ranowangko', 'code' => 'RNW'],
            ['name' => 'Wilayah 7 - KuntungMu\'ukur', 'code' => 'KNT'],
        ];
        foreach ($regions as $data) {
            \App\Models\Region::firstOrCreate(
                ['name' => $data['name']],
                ['code' => $data['code']]
            );
        }
    }
}
