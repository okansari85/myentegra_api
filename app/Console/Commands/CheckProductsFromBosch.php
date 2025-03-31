<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Interfaces\IBotApi\IBosch;

use App\Models\Products;

use DOMDocument;
use Carbon\Carbon;

class CheckProductsFromBosch extends Command
{

    protected $signature = 'check:bosch-products';
    protected $description = 'Bosha Bağlanıp Fiyat ve Stokları Günceller';

    private IBosch $boschService;

    // Constructor ile bağımlılık enjeksiyonu
    public function __construct(IBosch $_boschService)
    {
        parent::__construct();
        $this->boschService = $_boschService;
    }


    public function handle()
    {

        $products = Products::where('supplier_id', 1)->get();
        $this->info("Toplam " . $products->count() . " ürün bulundu.");

    }
}
