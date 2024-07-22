<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Interfaces\IN11Api\IOrder;

use App\Models\Buyers;
use App\Models\BuyerAdress;
use App\Models\Orders;
use App\Models\N11OrderItems;
use App\Models\N11Products;
use App\Models\OrderItems;
use App\Models\RelProductsN11Products;

use Carbon\Carbon;
use App\Enum\OrderStatusEnum;

class GetAndUpdateOrders implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;
    public $tries = 3;
    private IOrder $orderService;

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
    public function handle(IOrder $orderService): void
    {
        //
        try {


                $order = $orderService->orderDetail($this->order->id);

                $createdate = $order->orderDetail->createDate ?? '';


                $item_is_array = is_array ($order->orderDetail->itemList->item);

                $order_status = $item_is_array ? $order->orderDetail->itemList->item[0]->status : $order->orderDetail->itemList->item->status;
                $shippingCompanyName = $item_is_array ? $order->orderDetail->itemList->item[0]->shipmentInfo->shipmentCompany->name : $order->orderDetail->itemList->item->shipmentInfo->shipmentCompany->name;
                $campaignNumber  = $item_is_array ? $order->orderDetail->itemList->item[0]->shipmentInfo->campaignNumber : $order->orderDetail->itemList->item->shipmentInfo->campaignNumber;
                $shippedDate = $item_is_array ? $order->orderDetail->itemList->item[0]->shippingDate : $order->orderDetail->itemList->item->shippingDate;
                $dueAmount = $order->orderDetail->billingTemplate->sellerInvoiceAmount;



                if ($createdate != '') {
                    $createdate = Carbon::createFromFormat('d/m/Y H:i', $createdate);
                    $createdate = $createdate->format('Y-m-d H:i:s');
                }

                if ($shippedDate != '') {
                    $shippedDate = Carbon::createFromFormat('d/m/Y', $shippedDate)->format('Y-m-d');
                }

                /*item eğer array ise item statuslerini kontrol et eğer faklı ise siparişi mükkerer olarak kaydet*/

                switch ($order_status)
                {
                    case '5':
                        $order_status = OrderStatusEnum::NEW_ORDER;
                        break;
                    case '6':
                        $order_status = OrderStatusEnum::SHIPPED;
                        break;
                    case '7':
                        $order_status = OrderStatusEnum::SHIPPED;
                        break;
                    case '10':
                        $order_status = OrderStatusEnum::COMPLETED;
                        break;
                    case '15':
                        $order_status = OrderStatusEnum::DELAYED_SHIPPING;
                        break;

                    default:
                        $order_status = 0;
                        break;
                }



                //buyer yoksa ekle firstorCreate
                $buyer = Buyers::firstOrCreate(
                    ['buyer_id' => $order->orderDetail->buyer->id],
                    [
                        'fullName' => $order->orderDetail->shippingAddress->fullName,
                        'taxId' => $order->orderDetail->buyer->taxId,
                        'taxOffice' => $order->orderDetail->buyer->taxOffice,
                        'email' => $order->orderDetail->buyer->email,
                        'tcId' => $order->orderDetail->buyer->tcId ?? '',
                    ]
                );


                $buyer_adress = BuyerAdress::firstOrCreate(
                    [
                        'buyer_id' => $buyer->id,
                        'adressType' => 1
                    ],
                    [
                        'address' => $order->orderDetail->shippingAddress->address,
                        'fullName' => $order->orderDetail->shippingAddress->fullName,
                        'city' => $order->orderDetail->shippingAddress->city,
                        'district' => $order->orderDetail->shippingAddress->district,
                        'neighborhood' => $order->orderDetail->shippingAddress->neighborhood,
                        'postalCode' => $order->orderDetail->shippingAddress->postalCode,
                        'gsm' => $order->orderDetail->shippingAddress->gsm,
                        'tcId' => $order->orderDetail->shippingAddress->tcId ?? '',
                        'taxId' => $order->orderDetail->shippingAddress->taxId,
                        'taxHouse' => $order->orderDetail->shippingAddress->taxHouse,
                    ]
                );

                $buyer_adress = BuyerAdress::firstOrCreate(
                    [
                        'buyer_id' => $buyer->id,
                        'adressType' => 2
                    ],
                    [
                        'address' => $order->orderDetail->billingAddress->address,
                        'fullName' => $order->orderDetail->billingAddress->fullName,
                        'city' => $order->orderDetail->billingAddress->city,
                        'district' => $order->orderDetail->billingAddress->district,
                        'neighborhood' => $order->orderDetail->billingAddress->neighborhood,
                        'postalCode' => $order->orderDetail->billingAddress->postalCode,
                        'gsm' => $order->orderDetail->billingAddress->gsm,
                        'tcId' => $order->orderDetail->billingAddress->tcId ?? '',
                        'taxId' => $order->orderDetail->billingAddress->taxId,
                        'taxHouse' => $order->orderDetail->billingAddress->taxHouse,
                    ]
                );


                $order_data =  [
                    'orderDate' => $createdate, //"createDate": "22/06/2024 18:42",
                    'platformId' => 1,
                    'market_order_id' => $order->orderDetail->id ?? '', //"id": 353469682,
                    'market_order_number' => $order->orderDetail->orderNumber ?? '', //"orderNumber": "202669423236",
                    'status' => $order_status, // "status": 2,
                    'invoiceType' => $order->orderDetail->invoiceType ?? '', //"invoiceType": "2",
                    'paymentType' => $order->orderDetail->paymentType ?? '', //"paymentType": 8,
                    'buyer_id' => $buyer->id,
                    'shippingCompanyName' => $shippingCompanyName,
                    'campaignNumber' => $campaignNumber,
                    'dueAmount' => number_format((float)$dueAmount, 2, '.', ''),
                    'buyerable_id' => $buyer->id,
                    'buyerable_type' => Buyers::class,
                ];

                if ($order_status == OrderStatusEnum::SHIPPED){
                    $orderData['shippedDate'] = $shippedDate;
                }



                $order_record = Orders::updateOrCreate(
                        [
                        'market_order_id' =>  $order->orderDetail->id
                        ],
                        $order_data
            );


                $order_record_id= $order_record->id;

                if ($item_is_array){
                    foreach ($order->orderDetail->itemList->item as $item) {
                        $product_id = $item->productId;
                        $n11_product_id = $this->checkProductExistAndReturnId($product_id);
                        $this->addN11OrderItem($item, $order_record_id, $n11_product_id);

                    }


                }
                else {
                    $product_id = $order->orderDetail->itemList->item->productId;
                    $n11_product_id = $this->checkProductExistAndReturnId($product_id);
                    $this->addN11OrderItem($order->orderDetail->itemList->item, $order_record_id, $n11_product_id);
                }


        }
        catch (\Exception $e){
            \Log::error('Error updating order: ' . $e->getMessage());
        }
    }



    public function checkProductExistAndReturnId($product_id){

        $is_product_exist = N11Products::where('n11_id', $product_id)->first();

        if (!$is_product_exist) {
            return 0;
        }
        else
        {

            return $is_product_exist->id;
        }

    }

    public function addN11OrderItem($item,$order_id,$n11_product_id){

        try{
        $n11OrderItem = N11OrderItems::updateOrCreate(
            [
                'item_id' =>  $item->id
            ],
            [
            'order_id' => $order_id ?? 0,
            'n11_product_id' =>  $n11_product_id ?? 0,

            'productId' =>  $item->productId,
            //'deliveryFeeType' => $item->deliveryFeeType ?? 0,
            'productSellerCode' => $item->productSellerCode ?? '',
            'status' => $item->status ?? 0,
            //'approvedDate' => Carbon::createFromFormat('d/m/Y', $item->approvedDate)->format('Y-m-d'),
            //'dueAmount' =>  $item->dueAmount ?? 0.00,
            //'installmentChargeWithVAT' => $item->installmentChargeWithVAT ?? 0.00,
            'price' => $item->price ?? 0.00,
            //'totalMallDiscountPrice' => $item->totalMallDiscountPrice ?? 0.00,
            'quantity' => $item->quantity ?? 0,
            //'sellerCouponDiscount' => $item->sellerCouponDiscount ?? null,
            //'sellerStockCode' => $item->sellerStockCode ?? '',
            //'version' => $item->version ?? 0,
            //'attributes' => $item->attributes ?? {},
            'sellerDiscount' => $item->sellerDiscount,
            //'mallDiscount' => $item->mallDiscount,
            'commission' => $item->commission,
            'sellerInvoiceAmount' => $item->sellerInvoiceAmount,
            'productName' => $item->productName,
            'shippingDate' => $item->shippingDate ?? Carbon::createFromFormat('d/m/Y', $item->shippingDate)->format('Y-m-d'),
            //'customTextOptionValues' => $item->customTextOptionValues,
            //'shipmenCompanyCampaignNumber' => $item->shipmenCompanyCampaignNumber,
            // Eğer varsa diğer tüm sütunları buraya ekleyin
        ]);

        //n11 item asıl producta bağlı mı ?
        $n11_product = RelProductsN11Products::where('n11_id', $n11_product_id)->first();


        $orderItemData = [
            'order_id' => $order_id,
            'is_confirmed' => 0
            // Buraya OrderItem için diğer gerekli alanları ekleyebilirsiniz
        ];

        // Asıl product mevcutsa, product_id'yi ekle
        if (!is_null($n11_product)) {
            $orderItemData['product_id'] = $n11_product->product_id;
        }


        $orderItem = OrderItems::updateOrCreate(
            [
                'orderable_id' => $n11OrderItem->id,
                'orderable_type' => N11OrderItems::class,
            ],
            $orderItemData
        );

        $n11OrderItem->orderItem()->save($orderItem);


        }
        catch (\Exception $e){
            return $e->getMessage();
            echo  $e->getMessage();
        }

    }
}
