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
        Schema::create('n11_order_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('n11_product_id')->nullable()->constrained('n11_products');

            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('productId')->nullable();
            $table->integer('deliveryFeeType')->nullable();
            $table->string('productSellerCode')->nullable();
            $table->string('status')->nullable();
            $table->date('approvedDate')->nullable();
            $table->decimal('dueAmount', 10, 2)->nullable();
            $table->decimal('installmentChargeWithVAT', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('totalMallDiscountPrice', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('sellerCouponDiscount', 10, 2)->nullable();
            $table->string('sellerStockCode')->nullable();
            $table->integer('version')->nullable();
            $table->json('attributes')->nullable();
            $table->decimal('sellerDiscount', 10, 2)->nullable();
            $table->decimal('mallDiscount', 10, 2)->nullable();
            $table->decimal('commission', 10, 2)->nullable();
            $table->decimal('sellerInvoiceAmount', 10, 2)->nullable();
            $table->string('productName')->nullable();
            $table->date('shippingDate')->nullable();
            $table->json('customTextOptionValues')->nullable();
            $table->string('shipmenCompanyCampaignNumber')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('n11_order_items');
    }
};
