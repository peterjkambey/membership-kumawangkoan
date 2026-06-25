<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'admin@kumawangkoan.org')->first();
if ($user) {
    $user->password = 'admin123'; // hashed cast will bcrypt
    $user->save();
    echo "Password updated for: " . $user->email . "\n";
} else {
    $user = App\Models\User::create([
        'name' => 'Super Admin',
        'email' => 'admin@kumawangkoan.org',
        'password' => 'admin123', // hashed cast will bcrypt
    ]);
    $user->assignRole('super-admin');
    echo "User created: " . $user->email . "\n";
}
