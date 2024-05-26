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
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('market_order_id')->nullable();
            $table->unsignedBigInteger('market_order_number')->nullable();
            $table->boolean('is_confirmed')->default(0);
            $table->boolean('is_invoice_issued')->default(0);
            $table->integer('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dropColumn('market_order_id');
            $table->dropColumn('market_order_number');
            $table->dropColumn('is_confirmed');
            $table->dropColumn('is_invoice_issued');
            $table->dropColumn('status');

        });
    }
};
