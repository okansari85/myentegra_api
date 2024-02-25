<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $products = [
            [
            'productCode' => '00576101',
            'productTitle' => 'Bosch Siemens Profilo Çamaşır Kurutma Makinesi Ara Bağlantı Parçası',
            'price' => 468.00,
            'desi' => 5,
            'stock' => 2000,
            'profit_rate' => 1.25],
            [
            'productCode' => '00577549',
            'productTitle' => 'Bosch Elektrikli Süpürge Toz Torbası (G ALL)',
            'price' => 186.66,
            'desi' => 1,
            'stock' => 210,
            'profit_rate' => 1.25],
            [
            'productCode' => '00638233',
            'productTitle' => 'Bosch / Siemens / Profilo Elektrikli Süpürge Için Orijinal Hava Filtresi',
            'price' => 136.00,
            'desi' => 1,
            'stock' => 86,
            'profit_rate' => 1.25],
            [
            'productCode' => '00635924',
            'productTitle' => 'Bosch Buzdolabları Yumurtalık 4 lü',
            'price' => 62,
            'desi' => 1,
            'stock' => 2000,
            'profit_rate' => 1.25],
            [
            'productCode' => '17003048',
            'productTitle' => 'Bosch Siemens Elektrikli Süpürgeler Için Toz Torbası (G Tip) ',
            'price' => 146.66,
            'desi' => 1,
            'stock' => 2000,
            'profit_rate' => 1.25],
            [
            'productCode' => '00665340',
            'productTitle' => 'Bosch Siemens Profilo Buzdolabı Cam Raf',
            'price' => 205.34,
            'desi' => 5,
            'stock' => 145,
            'profit_rate' => 1.25],
            [
            'productCode' => '00659778',
            'productTitle' => 'Siemens Mq Serisi Mikser Çırpma Teli Seti',
            'price' => 140.00,
            'desi' => 2,
            'stock' => 5,
            'profit_rate' => 1.25],

        ];

        // Ana kategorileri veritabanına ekleyin
        DB::table('products')->insert($products);
    }
}
