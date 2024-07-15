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

            $order_record = Orders::updateOrCreate(
                [
                'market_order_id' =>  $order['items'][0]['orderNumber']
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
        catch (\Exception $e) {
            \Log::error('Error updating order: ' . $e->getMessage());
        }
    }
}
