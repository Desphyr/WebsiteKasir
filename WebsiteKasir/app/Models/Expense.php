<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'description', 'amount', 'expense_date'];

    protected $casts = [
        'expense_date' => 'date',
    ];

    // Relasi: Pengeluaran dicatat oleh satu User (Admin)
    public function user() {
        return $this->belongsTo(User::class);
    }
}

