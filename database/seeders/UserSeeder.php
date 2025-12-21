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
            'email' => 'alfianti@testmail.com', // Email untuk testing reset password
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. User Kasir: Salsabila
        User::create([
            'full_name' => 'Salsabila',
            'username' => 'salsabila',
            'email' => 'salsabila@testmail.com', // Email untuk testing reset password
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);

        // 3. User tambahan untuk testing
        User::create([
            'full_name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'testadmin@testmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }
}
