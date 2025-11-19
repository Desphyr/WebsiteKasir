<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name', 'image_url', 'price', 'stock'];

    // Relasi: Produk memiliki satu Kategori
    public function category() {
        return $this->belongsTo(Category::class);
    }

    // Relasi: Produk ada di banyak Detail Transaksi
    public function transactionDetails() {
        return $this->hasMany(TransactionDetail::class);
    }
}
