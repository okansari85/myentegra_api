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
        Schema::create('hb_listings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('listing_id')->nullable();
            $table->string('unique_identifier')->nullable();
            $table->string('hepsiburada_sku');
            $table->string('merchant_sku');
            $table->decimal('price', 8, 2);
            $table->integer('available_stock');
            $table->integer('dispatch_time');
            $table->string('cargo_company1')->nullable();
            $table->string('cargo_company2')->nullable();
            $table->string('cargo_company3')->nullable();
            $table->string('shipping_address_label');
            $table->string('shipping_profile_name');
            $table->string('claim_address_label');
            $table->integer('maximum_purchasable_quantity');
            $table->integer('minimum_purchasable_quantity');
            $table->decimal('final_price', 8, 2);
            $table->timestamp('pricing_start_date');
            $table->timestamp('pricing_end_date');
            $table->string('debtor_name');
            $table->decimal('debtor_amount', 8, 2);
            $table->boolean('is_salable');
            $table->json('customizable_properties')->nullable();
            $table->json('deactivation_reasons')->nullable();
            $table->boolean('is_suspended');
            $table->boolean('is_locked');
            $table->json('lock_reasons')->nullable();
            $table->boolean('is_frozen');
            $table->json('freeze_reasons')->nullable();
            $table->decimal('commission_rate', 5, 2);
            $table->json('available_warehouses')->nullable();
            $table->boolean('is_fulfilled_by_hb');
            $table->boolean('price_increase_disabled');
            $table->boolean('price_decrease_disabled');
            $table->boolean('stock_decrease_disabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hb_listings');
    }
};
