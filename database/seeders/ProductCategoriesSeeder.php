<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = [
            ['name' => 'Elektrikli Mutfak Aletleri', 'parent_id' => 0, 'order' => 0],
            ['name' => 'Beyaz Eşya', 'parent_id' => 0, 'order' => 1],
            ['name' => 'Elektrikli Süpürgeler', 'parent_id' => 0, 'order' => 2],
            ['name' => 'Temizlik ve Bakım Ürünleri', 'parent_id' => 0, 'order' => 3],
        ];

        // Ana kategorileri veritabanına ekleyin
        DB::table('product_categories')->insert($categories);
    }
}
