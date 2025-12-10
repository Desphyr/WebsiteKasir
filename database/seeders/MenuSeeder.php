<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $bakaranCategory = Category::firstOrCreate([
            'name' => 'Bakaran'
        ]);

        $menus = [
            ['name' => 'Tempura Panjang', 'price' => 1000, 'stock' => 50],
            ['name' => 'Tempura Gepeng',  'price' => 1000, 'stock' => 50],
            ['name' => 'Sosis Salju',     'price' => 1000, 'stock' => 50],
            ['name' => 'Scallop',         'price' => 1000, 'stock' => 50],
            ['name' => 'Sosis Bintang',   'price' => 1000, 'stock' => 50],
            ['name' => 'Sukoi',           'price' => 1000, 'stock' => 50],
            ['name' => 'Tahu Bakso',      'price' => 1000, 'stock' => 50],
            ['name' => 'Pentol Bakar',    'price' => 2000, 'stock' => 50],
            ['name' => 'Pentol Kotak',    'price' => 2000, 'stock' => 50],
            ['name' => 'Odeng',           'price' => 2000, 'stock' => 50],
            ['name' => 'Otak-Otak',       'price' => 2000, 'stock' => 50],
            ['name' => 'Sosis Merah',     'price' => 2000, 'stock' => 50],
            ['name' => 'Sate Lok-Lok 3 Tusuk', 'price' => 5000, 'stock' => 50],
            ['name' => 'Sosis Bakar Mini',     'price' => 5000, 'stock' => 50],
            ['name' => 'Dimsum Bakar Mini',    'price' => 8000, 'stock' => 50],
            ['name' => 'Sosis Bakar Jumbo',    'price' => 10000, 'stock' => 50],
            ['name' => 'Dimsum Bakar Jumbo',   'price' => 10000, 'stock' => 50],
        ];

        foreach ($menus as $item) {
            Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                    'category_id' => $bakaranCategory->id,
                ]
            );
        }
    }
}