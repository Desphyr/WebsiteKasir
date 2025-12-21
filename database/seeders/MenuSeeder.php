<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat atau cari Kategori
        $bakaranCategory = Category::firstOrCreate([
            'name' => 'Bakaran'
        ]);

        // 2. Data Menu (Key gambar sudah disesuaikan jadi 'image_url')
        $menus = [
            [
                'name'      => 'Tempura Panjang', 
                'price'     => 1000, 
                'stock'     => 50,
                'image_url' => 'https://asset.kompas.com/crops/u7IwqbXIviug8cdzl2kwR2zRUac=/0x0:0x0/1200x800/data/photo/2013/03/20/1218141-resep-tempura-kacang-panjang-p.jpg'
            ],
            [
                'name'      => 'Tempura Gepeng',  
                'price'     => 1000, 
                'stock'     => 50,
                'image_url' => 'https://i0.wp.com/i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/2d8de444-ea35-4e2f-b821-181f08177e92_Go-Biz_20210622_163819.jpeg'
            ],
            [
                'name'      => 'Sosis Salju',     
                'price'     => 1000, 
                'stock'     => 50,
                'image_url' => 'https://i0.wp.com/i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/2f54d64d-2b58-4dce-a647-c42a0863717b_Go-Biz_20221114_162930.jpeg'
            ],
            [
                'name'      => 'Scallop',         
                'price'     => 1000, 
                'stock'     => 50,
                'image_url' => 'https://superapp.id/blog/wp-content/uploads/2020/11/menueditor_item_07ccc30ab4cc41748cd46cef480765b1_1580432743275111453-817x690.jpg'
            ],
            [
                'name'      => 'Sosis Bintang',   
                'price'     => 1000, 
                'stock'     => 50,
                'image_url' => 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/849a599b-7fa5-4d12-8ad0-2e32135e40ab_Go-Biz_20240319_010755.jpeg'
            ],
            [
                'name'      => 'Sukoi',           
                'price'     => 1000, 
                'stock'     => 50,
                'image_url' => 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/71cb16ad-4765-47a5-9dca-2379e7b30e16_Go-Biz_20220913_105458.jpeg'
            ],
            [
                'name'      => 'Tahu Bakso',      
                'price'     => 1000, 
                'stock'     => 50,
                'image_url' => 'https://img-global.cpcdn.com/recipes/30f824d8f1ee84f6/680x781cq80/tahu-bakso-foto-resep-utama.jpg'
            ],
            [
                'name'      => 'Pentol Bakar',    
                'price'     => 2000, 
                'stock'     => 50,
                'image_url' => 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/785cf339-458c-4c53-a73f-dbfe27b2c281_Go-Biz_20210821_110014.jpeg'
            ],
            [
                'name'      => 'Pentol Kotak',    
                'price'     => 2000, 
                'stock'     => 50,
                'image_url' => 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/88f564f0-4564-4251-bdb9-26a96530d2ae_Go-Biz_20250412_160152.jpeg'
            ],
            [
                'name'      => 'Odeng',           
                'price'     => 2000, 
                'stock'     => 50,
                'image_url' => 'https://img-global.cpcdn.com/recipes/ebc1b5d141ae297f/680x781f0.5_0.5_1.0q80/odeng-foto-resep-utama.jpg'
            ],
            [
                'name'      => 'Otak-Otak',       
                'price'     => 2000, 
                'stock'     => 50,
                'image_url' => 'https://i0.wp.com/resepkoki.id/wp-content/uploads/2016/03/Resep-Otak-otak-goreng.jpg?fit=1820%2C1920&ssl=1'
            ],
            [
                'name'      => 'Sosis Merah',     
                'price'     => 2000, 
                'stock'     => 50,
                'image_url' => 'https://img-global.cpcdn.com/recipes/981453eed25a2f66/680x781cq80/sate-sosis-bakar-foto-resep-utama.jpg'
            ],
            [
                'name'      => 'Sate Lok-Lok 3 Tusuk', 
                'price'     => 5000, 
                'stock'     => 50,
                'image_url' => 'https://filebroker-cdn.lazada.co.id/kf/Sb82c662c99704d75b15178a83181eaa9h.jpg'
            ],
            [
                'name'      => 'Sosis Bakar Mini',     
                'price'     => 5000, 
                'stock'     => 50,
                'image_url' => 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/fff5b21b-67e2-4879-b3ad-cdac6784cb60_Go-Biz_20241228_111207.jpeg'
            ],
            [
                'name'      => 'Dimsum Bakar Mini',    
                'price'     => 8000, 
                'stock'     => 50,
                'image_url' => 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/25296caf-0636-48ae-9bb7-49e4c8504f93_Go-Biz_20211121_143930.jpeg'
            ],
            [
                'name'      => 'Sosis Bakar Jumbo',    
                'price'     => 10000, 
                'stock'     => 50,
                'image_url' => 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/b6d67dcb-b424-4b55-a2ba-d3180af1b1b9_Go-Biz_20240716_190611.jpeg'
            ],
            [
                'name'      => 'Dimsum Bakar Jumbo',   
                'price'     => 10000, 
                'stock'     => 50,
                'image_url' => 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/4a4c2c9e-0f88-4564-803e-f339965b9812_Go-Biz_20240207_163814.jpeg'
            ],
        ];

        // 3. Looping Insert ke Database
        foreach ($menus as $item) {
            Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'price'       => $item['price'],
                    'stock'       => $item['stock'],
                    'category_id' => $bakaranCategory->id,
                    'image_url'   => $item['image_url'], // Sesuai kolom database
                ]
            );
        }
    }
}