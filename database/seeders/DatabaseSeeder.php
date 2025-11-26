<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil UserSeeder yang baru kita buat
        $this->call([
            UserSeeder::class,
            // Anda bisa tambahkan Seeder lain di sini,
            // misal: CategorySeeder::class, ProductSeeder::class
        ]);
    }
}
