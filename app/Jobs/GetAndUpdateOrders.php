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
                $order_status = $this->order->status;

                //buyer yoksa ekle firstorCreate
                $buyer = Buyers::firstOrCreate(
                    ['buyer_id' => $order->orderDetail->buyer->id],
                    [
                        'fullName' => $order->orderDetail->shippingAddress->fullName,
                        'taxId' => $order->orderDetail->buyer->taxId,
                        'taxOffice' => $order->orderDetail->buyer->taxOffice,
                        'email' => $order->orderDetail->buyer->email,
                        'tcId' => $order->orderDetail->buyer->tcId,
                    ]
                );

                //echo $buyer->id;

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
                        'tcId' => $order->orderDetail->shippingAddress->tcId,
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
                        'tcId' => $order->orderDetail->billingAddress->tcId,
                        'taxId' => $order->orderDetail->billingAddress->taxId,
                        'taxHouse' => $order->orderDetail->billingAddress->taxHouse,
                    ]
                );



                $order_record = Orders::updateOrCreate(
                    [
                    'market_order_id' =>  $order->orderDetail->id
                    ],
                    [
                        'orderDate' => $order->orderDetail->createDate,
                        'platformId' => 1,
                        'isPaymentMade' => 0,
                        'market_order_id' => $order->orderDetail->id,
                        'market_order_number' => $order->orderDetail->orderNumber,
                        'is_confirmed' => 0,
                        'is_invoice_issued' => 0,
                        'status' => $order->orderDetail->status,
                        'invoiceType' => $order->orderDetail->invoiceType,
                        'paymentType' => $order->orderDetail->paymentType,
                        'buyer_id' => $buyer->id
                ]);

                //order shipments
                $orderShipments = $order->orderDetail->itemList;
                $orderShipmentsArray = (array) $orderShipments;

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





        }
        catch (\Exception $e){
            \Log::error('Error updating order: ' . $e->getMessage());
        }
    }
}
