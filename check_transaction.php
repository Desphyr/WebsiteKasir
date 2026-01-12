<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Cek transaksi ID 3
$transaction = \App\Models\Transaction::with('user')->find(3);

if ($transaction) {
    echo "Transaction ID: " . $transaction->id . PHP_EOL;
    echo "User ID: " . ($transaction->user_id ?? 'NULL') . PHP_EOL;
    
    if ($transaction->user) {
        echo "User Name: " . $transaction->user->name . PHP_EOL;
    } else {
        echo "User: NULL (relasi tidak ditemukan)" . PHP_EOL;
    }
    
    echo "\nSemua transaksi dengan user:" . PHP_EOL;
    $allTransactions = \App\Models\Transaction::with('user')->take(5)->get();
    foreach ($allTransactions as $t) {
        echo "  ID: {$t->id}, User ID: " . ($t->user_id ?? 'NULL') . ", User Name: " . ($t->user ? $t->user->name : 'NULL') . PHP_EOL;
    }
} else {
    echo "Transaksi ID 3 tidak ditemukan." . PHP_EOL;
}
