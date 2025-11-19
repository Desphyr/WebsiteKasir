<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. User Admin: Alfianti
        User::create([
            'full_name' => 'Alfianti',
            'username' => 'alfianti',
            'email' => 'alfianti@example.com', // Email opsional, boleh diisi
            'password' => Hash::make('password123'), // Ganti 'password123' dengan password aman
            'role' => 'admin',
        ]);

        // 2. User Kasir: Salsabila
        User::create([
            'full_name' => 'Salsabila',
            'username' => 'salsabila',
            'email' => 'salsabila@example.com', // Email opsional
            'password' => Hash::make('password123'), // Ganti 'password123' dengan password aman
            'role' => 'kasir',
        ]);
    }
}
