<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hb_order_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('hb_listing_id')->nullable()->constrained('hb_listings');

            $table->string('productName')->nullable();
            $table->unsignedBigInteger('orderNumber')->nullable();
            $table->timestamp('orderDate');
            $table->uuid('listing_id')->nullable();
            $table->uuid('lineItemId')->nullable();
            $table->uuid('merchantId')->nullable();
            $table->string('hbSku');
            $table->string('merchantSku');
            $table->integer('quantity');
            $table->decimal('price', 8, 2);
            $table->decimal('vat', 8, 2);
            $table->decimal('totalPrice', 8, 2)->nullable();
            $table->decimal('commission', 8, 2)->nullable();
            $table->decimal('commissionRate', 8, 2)->nullable();
            $table->decimal('unitHBDiscount', 8, 2)->nullable();
            $table->decimal('totalHBDiscount', 8, 2)->nullable();
            $table->decimal('unitMerchantDiscount', 8, 2)->nullable();
            $table->decimal('totalMerchantDiscount', 8, 2)->nullable();
            $table->decimal('merchantUnitPrice', 8, 2)->nullable();
            $table->decimal('merchantTotalPrice', 8, 2)->nullable();
            $table->string('cargoPaymentInfo')->nullable();
            $table->string('deliveryType')->nullable();
            $table->decimal('vatRate', 8, 2);
            $table->string('warehouse')->nullable();
            $table->string('productBarcode')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hb_order_items');
    }
};
