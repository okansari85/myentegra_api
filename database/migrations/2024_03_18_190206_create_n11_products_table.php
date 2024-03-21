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
        Schema::create('n11_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('n11_id')->nullable();
            $table->string('title');
            $table->float("display_price",10,2);
            $table->float("price",10,2);
            $table->string('productSellerCode');
            $table->text('description');
            $table->unsignedBigInteger('n11_category_id')->nullable();
            $table->unsignedBigInteger('stock_item_n11_catalog_id')->nullable();
            $table->unsignedBigInteger('stock_item_quantity')->nullable();
            $table->string('shipmentTemplate');
            $table->unsignedBigInteger('approvalStatus')->nullable();
            $table->unsignedBigInteger('saleStatus')->nullable();
            $table->unsignedBigInteger('preparingDay')->nullable();
            $table->unsignedBigInteger('productCondition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('n11_products');
    }
};
