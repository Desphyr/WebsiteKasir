<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Cek user ID 4
$user = \App\Models\User::find(4);

if ($user) {
    echo "User ditemukan:" . PHP_EOL;
    echo "  ID: " . $user->id . PHP_EOL;
    echo "  Name: '" . ($user->name ?? 'NULL') . "'" . PHP_EOL;
    echo "  Username: '" . ($user->username ?? 'NULL') . "'" . PHP_EOL;
    echo "  Email: '" . ($user->email ?? 'NULL') . "'" . PHP_EOL;
    echo "  Role: '" . ($user->role ?? 'NULL') . "'" . PHP_EOL;
    
    echo "\nSemua kolom user:" . PHP_EOL;
    print_r($user->getAttributes());
} else {
    echo "User ID 4 tidak ditemukan." . PHP_EOL;
}

echo "\n\nSemua users:" . PHP_EOL;
$allUsers = \App\Models\User::all();
foreach ($allUsers as $u) {
    echo "  ID: {$u->id}, Name: '" . ($u->name ?? 'NULL') . "', Username: '" . ($u->username ?? 'NULL') . "', Role: {$u->role}" . PHP_EOL;
}
