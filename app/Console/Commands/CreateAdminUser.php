<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'membership:create-admin {email} {name} {password}';
    protected $description = 'Create a super admin user';

    public function handle(): int
    {
        $user = User::factory()->create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
        ]);
        $user->password = bcrypt($this->argument('password'));
        $user->save();
        $user->assignRole('super-admin');

        $this->info("Super admin created: {$user->email}");
        return Command::SUCCESS;
    }
}
