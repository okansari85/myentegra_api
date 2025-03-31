<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Interfaces\IHBApi\IListing;
use App\Models\HBListings;

class HBListingUpdate extends Command
{
    protected $signature = 'update:hb-listing';
    protected $description = 'Hb Listingleri günceller';

    private IListing $hbListingService;

    // Constructor ile bağımlılık enjeksiyonu
    public function __construct(IListing $hbListingService)
    {
        parent::__construct();
        $this->hbListingService = $hbListingService;
    }

    // Komutun çalışma metodu
    public function handle()
    {
        $this->info('HB listing güncelleniyor...');

        // API'ye yapılacak isteğin verilerini ayarla
        $searchData = [
            'offset' => '0',
            'limit' => '5000',
            'hbSkuList' => '',
        ];

        try {
            // API'den gelen listeyi al
            $hb_listing = $this->hbListingService->getListings($searchData);

            // JSON verisini decode et
            $hb_listing = json_decode($hb_listing, true);

            // Eğer JSON decode işlemi başarısızsa, hata mesajı ver
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('API cevabı geçersiz JSON formatında.');
                return;
            }

            // API'den gelen verilerin 'listings' anahtarının varlığını kontrol et
            if (empty($hb_listing['listings'])) {
                $this->error('API cevabı boş veya geçersiz.');
                return;
            }

            $hb_listing = $hb_listing['listings'];

            // Gelen veriyi işleme (Veritabanı işlemleri, model güncellemeleri vs.)
            foreach ($hb_listing as $listing) {
                // Veritabanında var olan veriyi güncelle veya yeni bir kayıt oluştur
                HBListings::updateOrCreate(
                    ['listing_id' => $listing['listingId']],  // Unique alan (Örnek: SKU)
                    [
                        'unique_identifier' => $listing['uniqueIdentifier'] ?? '',
                        'hepsiburada_sku' => $listing['hepsiburadaSku'] ?? '',
                        'merchant_sku' => $listing['merchantSku'] ?? '',
                        'price' => isset($listing['price']) ? (float)$listing['price'] : 0.00,
                        'available_stock' => $listing['availableStock'] ?? 0,
                        'dispatch_time' => $listing['dispatchTime'] ?? 0,
                        'cargo_company1' => $listing['cargoCompany1'] ?? '',
                        'cargo_company2' => $listing['cargoCompany2'] ?? '',
                        'cargo_company3' => $listing['cargoCompany3'] ?? '',
                        'shipping_address_label' => $listing['shippingAddressLabel'] ?? '',
                        'shipping_profile_name' => $listing['shippingProfileName'] ?? '',
                        'claim_address_label' => $listing['claimAddressLabel'] ?? '',
                        'maximum_purchasable_quantity' => $listing['maximumPurchasableQuantity'] ?? 0,
                        'minimum_purchasable_quantity' => $listing['minimumPurchasableQuantity'] ?? 0,
                        'is_salable' => $listing['isSalable'] ?? true,
                        'customizable_properties' => $listing['customizableProperties'] ?? [],
                        'deactivation_reasons' => $listing['deactivationReasons'] ?? [],
                        'is_suspended' => $listing['isSuspended'] ?? false,
                        'is_locked' => $listing['isLocked'] ?? false,
                        'lock_reasons' => $listing['lockReasons'] ?? [],
                        'is_frozen' => $listing['isFrozen'] ?? false,
                        'freeze_reasons' => $listing['freezeReasons'] ?? [],
                        'commission_rate' => isset($listing['commissionRate']) ? (float)$listing['commissionRate'] : 0.0,
                        'available_warehouses' => $listing['availableWarehouses'] ?? [],
                        'is_fulfilled_by_hb' => $listing['isFulfilledByHB'] ?? false,
                        'price_increase_disabled' => $listing['priceIncreaseDisabled'] ?? false,
                        'price_decrease_disabled' => $listing['priceDecreaseDisabled'] ?? false,
                        'stock_decrease_disabled' => $listing['stockDecreaseDisabled'] ?? false,
                        'updated_at' => Carbon::now(),
                    ]
                );
            }

            $this->info('HB listingler başarıyla güncellendi.');
        } catch (\Exception $e) {
            // Hata durumunda hata mesajını göster
            $this->error('Bir hata oluştu: ' . $e->getMessage());
        }
    }
}
