<?php
// Quick test to verify the transactions table structure
use Illuminate\Support\Facades\DB;

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $columns = DB::select('DESCRIBE transactions');
    echo "Current transactions table structure:\n";
    echo "=====================================\n";
    foreach ($columns as $column) {
        echo sprintf("%-20s %-15s %-5s %-10s %s\n", 
            $column->Field, 
            $column->Type, 
            $column->Null, 
            $column->Key, 
            $column->Default ?? 'NULL'
        );
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
