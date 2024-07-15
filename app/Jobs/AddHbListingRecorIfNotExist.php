<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;

use App\Interfaces\IHBApi\IListing;

use App\Models\HbListings;
use Carbon\Carbon;

class AddHbListingRecorIfNotExist implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;
    public $tries = 3;

    public function __construct($_order)
    {
        $this->order = $_order;
    }

    /**
     * Execute the job.
     */
    public function handle(IListing $hbListingService): void
    {
        //
        //itemsleri foreach dongüsüne sok
        $order = $this->order;
        $orderItems = $order['items'];

        foreach ($orderItems as $orderItem) {
            if (!HBListings::where('listing_id', $orderItem['listingId'])->exists()) {
                // Kayıt mevcut değilse yapılacak işlemler
                $hbSku =$orderItem['hbSku'];
                $this->addHbListing($hbSku,$hbListingService);
                echo "Kayıt mevcut değil.";
            }
        }
    }

    public function addHbListing($hbSku='',IListing $hbListingService){

        $searchData = array(
            'offset'=> '0',
            'limit'=> '5000',
            'hbSkuList'=> $hbSku,
        );

        $hb_listing= $hbListingService->getListings($searchData);
        $hb_listing = json_decode($hb_listing, true);
        $hb_listing = $hb_listing['listings'][0];


        $listing = HBListings::create([
            'listing_id' => $hb_listing['listingId'],
            'unique_identifier' => $hb_listing['uniqueIdentifier'] ?? '',
            'hepsiburada_sku' => $hb_listing['hepsiburadaSku'],
            'merchant_sku' => $hb_listing['merchantSku'],
            'price' => number_format((float)$hb_listing['price'], 2, '.', ''),
            'available_stock' => $hb_listing['availableStock'] ?? 0,
            'dispatch_time' => $hb_listing['dispatchTime'] ?? 0,
            'cargo_company1' => $hb_listing['cargoCompany1'] ?? '',
            'cargo_company2' => $hb_listing['cargoCompany2'] ?? '',
            'cargo_company3' => $hb_listing['cargoCompany3'] ?? '',
            'shipping_address_label' => $hb_listing['shippingAddressLabel'] ?? '',
            'shipping_profile_name' => $hb_listing['shippingProfileName'] ?? '',
            'claim_address_label' => $hb_listing['claimAddressLabel'] ?? '',
            'maximum_purchasable_quantity' => $hb_listing['maximumPurchasableQuantity'] ?? 0,
            'minimum_purchasable_quantity' => $hb_listing['minimumPurchasableQuantity'] ?? 0,
            'is_salable' => $hb_listing['isSalable'] ?? true,
            'customizable_properties' => $hb_listing['customizableProperties'] ?? [],
            'deactivation_reasons' => $hb_listing['deactivationReasons'] ?? [],
            'is_suspended' => $hb_listing['isSuspended'] ?? false,
            'is_locked' => $hb_listing['isLocked'] ?? false,
            'lock_reasons' => $hb_listing['lockReasons'] ?? [],
            'is_frozen' => $hb_listing['isFrozen'] ?? false,
            'freeze_reasons' => $hb_listing['freezeReasons'] ?? [],
            'commission_rate' => $hb_listing['commissionRate'],
            'available_warehouses' => $hb_listing['availableWarehouses'] ?? [],
            'is_fulfilled_by_hb' => $hb_listing['isFulfilledByHB'] ?? false,
            'price_increase_disabled' => $hb_listing['priceIncreaseDisabled'] ?? false,
            'price_decrease_disabled' => $hb_listing['priceDecreaseDisabled'] ?? false,
            'stock_decrease_disabled' => $hb_listing['stockDecreaseDisabled'] ?? false,
        ]);

    }
}
