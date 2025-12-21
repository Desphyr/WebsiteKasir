<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all();

foreach ($users as $user) {
    $user->update(['email' => strtolower($user->username) . '@testmail.com']);
    echo $user->username . ' updated to ' . $user->email . PHP_EOL;
}

echo "All users updated successfully!" . PHP_EOL;
