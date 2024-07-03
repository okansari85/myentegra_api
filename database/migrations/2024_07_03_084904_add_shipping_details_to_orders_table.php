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
            $table->string('shippingCompanyName')->nullable();
            $table->string('campaignNumber')->nullable();
            $table->float("dueAmount",10,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dropColumn('shippingCompanyName');
            $table->dropColumn('campaignNumber');
            $table->dropColumn('dueAmount');
        });
    }
};
