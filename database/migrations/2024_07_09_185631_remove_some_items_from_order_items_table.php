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
        Schema::table('order_items', function (Blueprint $table) {
            //
            $table->dropForeign('order_items_n11_product_id_foreign');
            $table->dropColumn('n11_product_id');
            $table->dropForeign('order_items_order_id_foreign');
            $table->dropColumn('order_id');
            $table->dropForeign('order_items_order_shipment_id_foreign');
            $table->dropColumn('order_shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            //
        });
    }
};
