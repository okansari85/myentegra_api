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
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('order_id')->constrained('orders');
            $table->string('trackingNumber');
            $table->string('shipmentCompanyName');
            $table->string('shipmentCompanyShortName');
            $table->string('shipmentCode');
            $table->integer('shipmentMethod');
            $table->integer('campaignNumberStatus');
            $table->integer('shippedDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};
