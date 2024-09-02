<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use App\Models\Buyers;
use App\Models\BuyerAdress;
use App\Models\Orders;
use App\Models\OrderItems;

use App\Models\PazaramaProduct;

use App\Models\PazaramaOrderItem;
use App\Models\RelProductsPazaramaProducts;


use Carbon\Carbon;
use App\Enum\OrderStatusEnum;

class GetAndUpdateOrdersFromPazarama implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;
    public $tries = 3;


    public function __construct($_order)
    {
        $this->order = $_order;
    }


    public function handle(): void
    {
        try{

            $order= $this->order;

            $buyer = Buyers::firstOrCreate(
                ['buyer_id' => $order['customerId']],
                [
                    'fullName' => $order['customerName'],
                    'taxId' => $order['billingAddress']['taxNumber'] ?? '',
                    'taxOffice' => $order['billingAddress']['taxOffice'] ?? '',
                    'email' => $order['customerEmail'],
                    'tcId' => $order['billingAddress']['identityNumber'] ?? '',
                ]
            );


            $buyer_adress = BuyerAdress::firstOrCreate(
                [
                    'buyer_id' => $buyer->id,
                    'adressType' => 1
                ],
                [
                    'address' => $order['shipmentAddress']['displayAddressText'],
                    'fullName' => $order['customerName'],
                    'city' =>  $order['shipmentAddress']['cityName'],
                    'district' => $order['shipmentAddress']['districtName'] ?? '',
                    'neighborhood' => $order['shipmentAddress']['neighborhoodName'] ?? '',
                    'postalCode' => '',
                    'gsm' => $order['shipmentAddress']['phoneNumber'] ?? '',
                    'tcId' => $order['billingAddress']['identityNumber'] ?? '',
                    'taxId' =>  $order['billingAddress']['taxNumber'] ?? '',
                    'taxHouse' => $order['billingAddress']['taxOffice'] ?? '',
                ]
            );


            $buyer_adress = BuyerAdress::firstOrCreate(
                [
                    'buyer_id' => $buyer->id,
                    'adressType' => 2
                ],
                [
                    'address' => $order['billingAddress']['displayAddressText'],
                    'fullName' => $order['customerName'],
                    'city' =>  $order['billingAddress']['cityName'],
                    'district' => $order['billingAddress']['districtName'] ?? '',
                    'neighborhood' => $order['billingAddress']['neighborhoodName'] ?? '',
                    'postalCode' => '',
                    'gsm' => $order['billingAddress']['phoneNumber'] ?? '',
                    'tcId' => $order['billingAddress']['identityNumber'] ?? '',
                    'taxId' =>  $order['billingAddress']['taxNumber'] ?? '',
                    'taxHouse' => $order['billingAddress']['taxOffice'] ?? '',
                ]
            );

            //format order date
            $createdate = $order['orderDate'] ?? ''; // [orderDate] => 2024-08-17 11:33

            if ($createdate != '') {
                $createdate = Carbon::createFromFormat('Y-m-d H:i', $createdate);
                $createdate = $createdate->format('Y-m-d H:i:s');
            }


            $order_status = $order['items'][0]['orderItemStatus'];

            /*
            OrderItemStatus int
            3: Siparişiniz Alındı
            12: Siparişiniz Hazırlanıyor
            13: Tedarik Edilemedi
            5: Siparişiniz Kargoya Verildi
            11: Teslim Edildi
            14: Teslim Edilemedi
            7: İade Süreci Başlatıldı
            8: İade Onaylandı
            9: İade Reddedildi
            10: İade Edildi
            */

            switch ($order_status)
            {
                case '3':
                    $order_status = OrderStatusEnum::NEW_ORDER;
                    break;
                case '12':
                    $order_status = OrderStatusEnum::NEW_ORDER;
                    break;
                case '5':
                    $order_status = OrderStatusEnum::SHIPPED;
                    break;
                case '11':
                    $order_status = OrderStatusEnum::COMPLETED;
                    break;

                default:
                    $order_status = 0;
                    break;
            }


            $market_order_id= $order['orderId'];
            //orderdata
            $order_data =     [
                'orderDate' => $createdate, //"createDate": "22/06/2024 18:42",
                'platformId' => 3,
                'status' => $order_status,
                'market_order_id' => $market_order_id, //"id": 353469682,
                'market_order_number' => $order['orderNumber'], //"orderNumber": "202669423236",
                'invoiceType' => $order['billingAddress']['taxNumber'] ? 2 : 1, //"invoiceType": "2",
                'paymentType' => $order['paymentType'], //"paymentType": 8,
                'buyer_id' => $buyer->id,
                'shippingCompanyName' => $order['items'][0]['cargo']['companyName'],
                'campaignNumber' => $order['items'][0]['shipmentCode'],
                'dueAmount' => number_format((float)$order['orderAmount'], 2, '.', ''),
                'buyerable_id' => $buyer->id,
                'buyerable_type' => Buyers::class,
            ];

            //orderstatus shipped olduğu zaman shipped date yi güncelle bir seferliğine
            $order_record = Orders::where('market_order_id', $market_order_id)->first();
            if ($order_status == OrderStatusEnum::SHIPPED && (empty($order_record->shippedDate) || $order_record->shippedDate == null)) {
                // O günün tarihini 'Y-m-d' formatında al ve shippedDate olarak kaydet
                $order_data['shippedDate'] = Carbon::now()->format('Y-m-d H:i:s');
            }



            //order status new order ve veritabanındaki kayıt
                //$order_status = OrderStatusEnum::NEW_ORDER;
            if ($order_record){
                    // yeni sipariş ve order status 2 ise bişey yapma
                    if (!($order_status == OrderStatusEnum::NEW_ORDER && $order_record->status == 2 )) {
                        //gecikmeye düşmüş ama status 2 ise de bişey yapma
                            $order_record = Orders::updateOrCreate(
                                [
                                'market_order_id' =>   $market_order_id
                                ],
                                $order_data
                            );
                    }
            }
            else {
                     // Kayıt yoksa yeni bir kayıt oluştur
                     $order_record = Orders::updateOrCreate(
                        [
                        'market_order_id' =>  $market_order_id
                        ],
                        $order_data
                    );
            }

            $order_record_id= $order_record->id;
            $order_items = $order['items'];

            foreach ($order_items as $item) {
                $product_id = $this->checkProductExistAndReturnId($item['product']['code']);
                $this->addPazaramaOrderItem($item, $order_record_id, $product_id);
            }

        }
        catch (\Exception $e) {
            \Log::error('Error updating order: ' . $e->getMessage());
        }
    }


    public function checkProductExistAndReturnId($code)
    {
        $product = PazaramaProduct::where('code', $code)->first();
        return optional($product)->id ?: 0;
    }

    public function addPazaramaOrderItem($item,$order_id,$pazarama_product_id){

        try{

        $pazaramaOrderItem = PazaramaOrderItem::updateOrCreate(
            [
                'orderItemId' => $item['orderItemId'], // Benzersiz anahtar olarak kullanılacak sütun
            ],
            [
                'order_id' => $order_id,
                'pazarama_product_id' => $pazarama_product_id,
                'orderItemStatus' => $item['orderItemStatus'],
                'orderItemStatusName' => $item['orderItemStatusName'],
                'shipmentCode' => $item['shipmentCode'],
                'shipmentCostCurrency' => $item['shipmentCost']['currency'],
                'shipmentCostValue' => $item['shipmentCost']['value'],
                'deliveryType' => $item['deliveryType'],
                'deliveryDetail' => $item['deliveryDetail'],
                'quantity' => $item['quantity'],
                'listPriceCurrency' => $item['listPrice']['currency'],
                'listPriceValue' => $item['listPrice']['value'],
                'salePriceCurrency' => $item['salePrice']['currency'],
                'salePriceValue' => $item['salePrice']['value'],
                'taxAmountCurrency' => $item['taxAmount']['currency'],
                'taxAmountValue' => $item['taxAmount']['value'],
                'shipmentAmountCurrency' => $item['shipmentAmount']['currency'],
                'shipmentAmountValue' => $item['shipmentAmount']['value'],
                'totalPriceCurrency' => $item['totalPrice']['currency'],
                'totalPriceValue' => $item['totalPrice']['value'],
                'discountAmountCurrency' => $item['discountAmount']['currency'],
                'discountAmountValue' => $item['discountAmount']['value'],
                'discountDescription' => $item['discountDescription'],
                'taxIncluded' => $item['taxIncluded'],
                'cargoCompanyId' => $item['cargo']['companyId'],
                'cargoCompanyName' => $item['cargo']['companyName'],
                'trackingNumber' => $item['cargo']['trackingNumber'],
                'trackingUrl' => $item['cargo']['trackingUrl'],
                'productId' => $item['product']['productId'],
                'productName' => $item['product']['name'],
                'productTitle' => $item['product']['title'],
                'productUrl' => $item['product']['url'],
                'productImageURL' => $item['product']['imageURL'],
                'productVariantOptionDisplay' => $item['product']['variantOptionDisplay'],
                'productStockCode' => $item['product']['stockCode'],
                'productCode' => $item['product']['code'],
                'productVatRate' => $item['product']['vatRate'],
            ]
        );


        $orderItemData = [
            'order_id' => $order_id,
            // Buraya OrderItem için diğer gerekli alanları ekleyebilirsiniz
        ];

        $existingOrderItem = OrderItems::where([
            'orderable_id' => $pazaramaOrderItem->id,
            'orderable_type' => PazaramaOrderItem::class
        ])->first();


        if ($existingOrderItem && $existingOrderItem->is_confirmed == 0) {

            $pazarama_product = RelProductsPazaramaProducts::where('pazarama_product_id', $pazarama_product_id)->first();

            if (!is_null($pazarama_product)) {
                $orderItemData['product_id'] = $pazarama_product->product_id;
            }

        }

        $orderItem = OrderItems::updateOrCreate(
            [
                'orderable_id' => $pazaramaOrderItem->id,
                'orderable_type' => PazaramaOrderItem::class,
            ],
            $orderItemData
        );

        $pazaramaOrderItem->orderItem()->save($orderItem);

    }
    catch (\Exception $e){
        return $e->getMessage();
        echo  $e->getMessage();
    }




    }


}
