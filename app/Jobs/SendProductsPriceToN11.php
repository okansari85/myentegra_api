<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Interfaces\IN11Api\IProduct;
use App\Interfaces\ICategoryCommision;
use App\Interfaces\ICargo;

use App\Models\N11CategoryCommission;
use App\Models\Products;

class SendProductsPriceToN11 implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product;
    public $tries = 3;
    private IProduct $productservice;

    /**
     * Create a new job instance.
     */
    public function __construct($_product)
    {
        //
        $this->product = $_product;
    }

    /**
     * Execute the job.
     */
    public function handle(IProduct $productservice,ICategoryCommision $categorycommisionservice,ICargo $cargoservice): void
    {

        try {
            $n11Product = $this->product['n11_product']['n11_product'];
            $n11Id = $n11Product['n11_id'];
            $n11CategoryId = $n11Product['n11_category_id'];
            $desi = $this->product['desi'];

            $totalCommission = $categoryCommissionService->getN11CategoryCommissionByCategoryId($n11CategoryId);
            $desiPrice = number_format((float)$cargoService->getCargoPriceByDesi($desi), 2, '.', '');

            $cost = Products::find($this->product['id'])->maliyet();
            $commission = number_format((float)(100 - $totalCommission), 2, '.', '');

            $displayPrice = ($cost + $desiPrice) / ($commission / 100);
            $displayPrice = number_format((float)$displayPrice, 2, '.', '');



            $productService->updateProductPriceById($n11Id, $displayPrice, 1, $this->product['productCode'], $displayPrice);
        } catch (\Exception $e) {
            // Handle any errors gracefully
            \Log::error('Error updating product price: ' . $e->getMessage());
        }







/*
        $n11_id=$this->product['n11_product']['n11_product']['n11_id'];
        $n11_category_id = $this->product['n11_product']['n11_product']['n11_category_id'];
        $desi = $this->product['desi'];

        $toplam_komisyon = $categorycommisionservice->getN11CategoryCommissionByCategoryId($n11_category_id);
        $desi_price = number_format((float)$cargoservice->getCargoPriceByDesi($desi), 2, '.', '');

        $maliyet = Products::find($this->product['id'])->maliyet();
        $kom = number_format((float)(100 - $toplam_komisyon), 2, '.', '');

        $display_price = ($maliyet + $desi_price) / ($kom /100);
        $display_price = number_format((float)$display_price, 2, '.', '');

        $a = $productservice->updateProductPriceById($n11_id,$display_price, 1 , $this->product['productCode'], $display_price);
        */

    }

}
