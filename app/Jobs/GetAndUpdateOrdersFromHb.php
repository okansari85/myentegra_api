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
use App\Models\RelProductsHbListings;
use App\Models\HBListings;
use App\Models\HbOrderItems;
use App\Models\OrderItems;

use Carbon\Carbon;
use App\Enum\OrderStatusEnum;

use App\Interfaces\IHBApi\IOrder;

class GetAndUpdateOrdersFromHb implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($_order)
    {
        //
        $this->order = $_order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        try {

            $order= $this->order;

            //buyer yoksa ekle firstorCreate
            $buyer = Buyers::firstOrCreate(
                ['buyer_id' => $order['customerId']],
                [
                    'fullName' => $order['recipientName'],
                    'taxId' => $order['taxNumber'] ?? '',
                    'taxOffice' => $order['taxOffice'] ?? '',
                    'email' => $order['email'],
                    'tcId' => $order['identityNo'] ?? '',
                ]
            );

            $buyer_adress = BuyerAdress::firstOrCreate(
                [
                    'buyer_id' => $buyer->id,
                    'adressType' => 1
                ],
                [
                    'address' => $order['shippingAddressDetail'],
                    'fullName' => $order['recipientName'],
                    'city' =>  $order['shippingCity'],
                    'district' => $order['shippingTown'] ?? '',
                    'neighborhood' => $order['shippingDistrict'] ?? '',
                    'postalCode' => '',
                    'gsm' => $order['phoneNumber'] ?? '',
                    'tcId' => $order['identityNo'] ?? '',
                    'taxId' => $order['taxNumber'] ?? '',
                    'taxHouse' => $order['taxOffice'] ?? '',
                ]
            );

            $buyer_adress = BuyerAdress::firstOrCreate(
                [
                    'buyer_id' => $buyer->id,
                    'adressType' => 2
                ],
                [
                    'address' => $order['billingAddress'],
                    'fullName' => $order['recipientName'],
                    'city' =>  $order['billingCity'],
                    'district' => $order['billingTown'],
                    'neighborhood' => $order['billingDistrict'],
                    'postalCode' => $order['billingPostalCode'] ?? '',
                    'gsm' => $order['phoneNumber'] ?? '',
                    'tcId' => $order['identityNo'] ?? '',
                    'taxId' => $order['taxNumber'] ?? '',
                    'taxHouse' => $order['taxOffice'] ?? '',
                ]
            );


            $date = Carbon::createFromFormat('Y-m-d\TH:i:s', $order['orderDate']);
            $formattedDate = $date->format('Y-m-d H:i:s');

            $order_record = Orders::where('market_order_id', $order['items'][0]['orderNumber'])->first();


            if ($order_record) {
                // Kayıt varsa ve statusü elleme
                    $order_record = Orders::updateOrCreate(
                        [
                        'market_order_id' =>  $order['items'][0]['orderNumber'],
                        ],
                         [
                        'orderDate' => $formattedDate, //"createDate": "22/06/2024 18:42",
                        'platformId' => 2,
                        'market_order_id' => $order['items'][0]['orderNumber'], //"id": 353469682,
                        'market_order_number' => $order['items'][0]['orderNumber'], //"orderNumber": "202669423236",
                        'invoiceType' => $order['taxNumber'] ? 2 : 1, //"invoiceType": "2",
                        'paymentType' => 0, //"paymentType": 8,
                        'buyer_id' => $buyer->id,
                        'shippingCompanyName' => $order['cargoCompany'],
                        'campaignNumber' => $order['barcode'],
                        'dueAmount' => number_format((float)$order['totalPrice']['amount'], 2, '.', ''),
                        'buyerable_id' => $buyer->id,
                        'buyerable_type' => Buyers::class,
                    ]);

            }
            else{

                $order_record = Orders::updateOrCreate(
                    [
                    'market_order_id' =>  $order['items'][0]['orderNumber'],
                    ],
                     [
                    'orderDate' => $formattedDate, //"createDate": "22/06/2024 18:42",
                    'platformId' => 2,
                    'market_order_id' => $order['items'][0]['orderNumber'], //"id": 353469682,
                    'market_order_number' => $order['items'][0]['orderNumber'], //"orderNumber": "202669423236",
                    'status' => 1,
                    'invoiceType' => $order['taxNumber'] ? 2 : 1, //"invoiceType": "2",
                    'paymentType' => 0, //"paymentType": 8,
                    'buyer_id' => $buyer->id,
                    'shippingCompanyName' => $order['cargoCompany'],
                    'campaignNumber' => $order['barcode'],
                    'dueAmount' => number_format((float)$order['totalPrice']['amount'], 2, '.', ''),
                    'buyerable_id' => $buyer->id,
                    'buyerable_type' => Buyers::class,
                ]);

            }




        $order_record_id= $order_record->id;

        $order_items = $order['items'];

        foreach ($order_items as $item) {
            $list_id = $this->checkProductExistAndReturnId($item['listingId']);
            $this->addHBOrderItem($item, $order_record_id, $list_id);
        }


        }
        catch (\Exception $e) {
            \Log::error('Error updating order: ' . $e->getMessage());
        }
    }

    public function addHBOrderItem($item,$order_id,$list_id){

        $hbOrderItem = HbOrderItems::updateOrCreate(
            [
                'lineItemId' => $item['lineItemId']
            ],
            [
            'order_id' => $order_id,
            'hb_listing_id' => $list_id,
            'productName' => $item['productName'] ?? '',
            'orderDate' => $item['orderDate'] ?  Carbon::createFromFormat('Y-m-d\TH:i:s', $item['orderDate'])->format('Y-m-d H:i:s') : '',
            'listing_id' => $item['listingId'] ?? '',
            'lineItemId'=> $item['lineItemId'] ?? '',
            'merchantId'=> $item['merchantId'] ?? '',
            'hbSku'=> $item['hbSku'] ?? '',
            'merchantSku'=> $item['merchantSku'] ?? '',
            'quantity' => (int)$item['quantity'],
            'price' => number_format((float)$item['price']['amount'], 2, '.', ''),
            'vat'=> number_format((float)$item['vat'], 2, '.', ''),
            'totalPrice'=> number_format((float)$item['totalPrice']['amount'], 2, '.', ''),
            'commission'=> number_format((float)$item['commission']['amount'], 2, '.', ''),
            'commissionRate'=> number_format((float)$item['commissionRate'], 2, '.', ''),
            'unitHBDiscount'=> number_format((float)$item['unitHBDiscount']['amount'], 2, '.', ''),
            'totalHBDiscount'=> number_format((float)$item['totalHBDiscount']['amount'], 2, '.', ''),
            'unitMerchantDiscount'=> number_format((float)$item['unitMerchantDiscount']['amount'], 2, '.', ''),
            'totalMerchantDiscount'=> number_format((float)$item['totalMerchantDiscount']['amount'], 2, '.', ''),
            'merchantUnitPrice'=> number_format((float)$item['merchantUnitPrice']['amount'], 2, '.', ''),
            'merchantTotalPrice'=> number_format((float)$item['merchantTotalPrice']['amount'], 2, '.', ''),
            'cargoPaymentInfo'=>$item['cargoPaymentInfo'] ?? '',
            'deliveryType'=>$item['deliveryType'] ?? '',
            'vatRate'=> number_format((float)$item['vatRate'], 2, '.', ''),
            'warehouse'=>$item['warehouse']['shippingAddressLabel'] ?? '',
            'productBarcode'=> $item['productBarcode'] ?? '',
            'orderNumber'=> $item['orderNumber'] ?? '',
        ]);

        $orderItemData = [
            'order_id' => $order_id,
            // Buraya OrderItem için diğer gerekli alanları ekleyebilirsiniz
        ];

            // Asıl product mevcutsa, product_id'yi ekle
            $hb_product = RelProductsHbListings::where('hb_listing_id', $list_id)->first();
            if (!is_null($hb_product)) {
                $orderItemData['product_id'] = $hb_product->product_id;
            }


        $orderItem = OrderItems::updateOrCreate(
            [
                'orderable_id' => $hbOrderItem->id,
                'orderable_type' => HbOrderItems::class,
            ],
            $orderItemData
        );

        $hbOrderItem->orderItem()->save($orderItem);


    }

    public function checkProductExistAndReturnId($listing_id){

        $is_product_exist = HBListings::where('listing_id', $listing_id)->first();

        if ($is_product_exist) {
            return $is_product_exist->id;
        }
        else
        {
            return 0;
        }

    }
}
