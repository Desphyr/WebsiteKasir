<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "Testing Transaction Creation...\n";
echo "==============================\n";

try {
    // Test creating a transaction with cash payment
    $transaction = Transaction::create([
        'user_id' => 2,
        'total_amount' => 23000.00,
        'payment_type' => 'Cash',
        'cash_amount' => 50000.00,
        'change_amount' => 27000.00,
        'transaction_time' => now(),
    ]);

    echo "✅ SUCCESS: Transaction created successfully!\n";
    echo "Transaction ID: " . $transaction->id . "\n";
    echo "Total Amount: Rp " . number_format($transaction->total_amount, 0, ',', '.') . "\n";
    echo "Cash Amount: Rp " . number_format($transaction->cash_amount, 0, ',', '.') . "\n";
    echo "Change Amount: Rp " . number_format($transaction->change_amount, 0, ',', '.') . "\n";
    
    // Clean up test data
    $transaction->delete();
    echo "\n🧹 Test transaction cleaned up.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "The fix may not be working properly.\n";
}
