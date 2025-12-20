<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Creating sample transactions...\n";

$user = User::where('role', 'kasir')->first();
$products = Product::all()->take(3);

if (!$user) {
    echo "No kasir user found!\n";
    exit;
}

for ($i = 1; $i <= 3; $i++) {
    $transaction = Transaction::create([
        'user_id' => $user->id,
        'total_amount' => 50000 * $i,
        'payment_type' => $i % 2 == 0 ? 'QRIS' : 'Cash',
        'transaction_time' => now()->subDays(rand(1, 30)),
    ]);
    
    foreach ($products as $product) {
        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => rand(1, 3),
            'price_per_item' => $product->price,
        ]);
    }
    
    echo "Created transaction #{$transaction->id}\n";
}

echo "Sample transactions created successfully!\n";
echo "Total transactions: " . Transaction::count() . "\n";
