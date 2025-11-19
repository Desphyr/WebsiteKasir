<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionDetail extends Model
{
    use HasFactory;
    protected $fillable = ['transaction_id', 'product_id', 'quantity', 'price_per_item'];

    // Relasi: Detail Transaksi milik satu Transaksi
    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi: Detail Transaksi untuk satu Produk
    public function product() {
        return $this->belongsTo(Product::class);
    }
}