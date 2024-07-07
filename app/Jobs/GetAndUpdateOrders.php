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
use App\Models\OrderShipments;

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

                $dueAmount = $order->orderDetail->billingTemplate->sellerInvoiceAmount;



                if ($createdate != '') {
                    $createdate = Carbon::createFromFormat('d/m/Y H:i', $createdate);
                    $createdate = $createdate->format('Y-m-d H:i:s');
                }

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



                $order_record = Orders::updateOrCreate(
                    [
                    'market_order_id' =>  $order->orderDetail->id
                    ],
                    [
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
                        'dueAmount' => number_format((float)$dueAmount, 2, '.', '')
                ]);












                $orderItems = (array) $order->orderDetail->itemList;



/*


                //order shipments

                $orderShipmentsArray = (array) $orderItems;

                foreach ($orderShipmentsArray as $item) {


                    $order_shipment_record = OrderShipments::updateOrCreate(
                        [
                        'order_id' =>  $order_record->id
                        ],
                        [
                            'order_id' => $order_record->id,
                            'trackingNumber' => '',
                            'shipmentCompanyName' => $item->shipmentInfo->shipmentCompany->name,
                            'shipmentCompanyShortName' => $item->shipmentInfo->shipmentCompany->shortName,
                            'shipmentCode' => $item->shipmentInfo->shipmentCode,
                            'shipmentMethod' => $item->shipmentInfo->shipmentMethod,
                            'campaignNumberStatus' => $item->shipmentInfo->campaignNumberStatus,
                            'shippedDate' => $item->shippingDate,
                            'campaginNumber' =>  $item->shipmentInfo->campaignNumber,
                    ]);



                }


*/


        }
        catch (\Exception $e){
            \Log::error('Error updating order: ' . $e->getMessage());
        }
    }
}
