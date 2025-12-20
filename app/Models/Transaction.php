<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'total_amount', 'payment_type', 'transaction_time', 'cash_amount', 'change_amount'];

    protected $casts = [
        'transaction_time' => 'datetime',
    ];

    // Relasi: Transaksi dilayani oleh satu User (Kasir)
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relasi: Transaksi memiliki banyak Detail Transaksi
    public function details() {
        return $this->hasMany(TransactionDetail::class);
    }
}