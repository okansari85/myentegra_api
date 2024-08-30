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
        Schema::create('pazarama_order_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('pazarama_product_id')->nullable()->constrained('pazarama_products');


            $table->string('orderItemId');
            $table->integer('orderItemStatus')->default(0);
            $table->string('orderItemStatusName')->nullable();
            $table->string('shipmentCode')->nullable();

            // Shipment cost
            $table->string('shipmentCostCurrency')->default('TL');
            $table->decimal('shipmentCostValue', 10, 2)->default(0);

            $table->integer('deliveryType')->default(0);
            $table->text('deliveryDetail')->nullable();
            $table->integer('quantity')->default(1);

            // Prices
            $table->string('listPriceCurrency')->default('TL');
            $table->decimal('listPriceValue', 10, 2)->default(0);
            $table->string('salePriceCurrency')->default('TL');
            $table->decimal('salePriceValue', 10, 2)->default(0);
            $table->string('taxAmountCurrency')->default('TL');
            $table->decimal('taxAmountValue', 10, 2)->default(0);
            $table->string('shipmentAmountCurrency')->default('TL');
            $table->decimal('shipmentAmountValue', 10, 2)->default(0);
            $table->string('totalPriceCurrency')->default('TL');
            $table->decimal('totalPriceValue', 10, 2)->default(0);
            $table->string('discountAmountCurrency')->default('TL');
            $table->decimal('discountAmountValue', 10, 2)->default(0);
            $table->text('discountDescription')->nullable();
            $table->boolean('taxIncluded')->default(0);

            // Cargo details
            $table->string('cargoCompanyId')->nullable();
            $table->string('cargoCompanyName')->nullable();
            $table->string('trackingNumber')->nullable();
            $table->string('trackingUrl')->nullable();

            // Product details
            $table->string('productId')->nullable();
            $table->string('productName')->nullable();
            $table->string('productTitle')->nullable();
            $table->string('productUrl')->nullable();
            $table->string('productImageURL')->nullable();
            $table->string('productVariantOptionDisplay')->nullable();
            $table->string('productStockCode')->nullable();
            $table->string('productCode')->nullable();
            $table->integer('productVatRate')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pazarama_order_items');
    }
};
