<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

/**
 * @property string $role
 */
class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, CanResetPasswordTrait;

    protected $fillable = [
        'full_name', 'username', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relasi: User (Kasir) melayani banyak Transaksi
    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    // Relasi: User (Admin) mencatat banyak Pengeluaran
    public function expenses() {
        return $this->hasMany(Expense::class);
    }
}
