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
        Schema::table('n11_cargo_prices', function (Blueprint $table) {
            //
            $table->float("aras_price",10,2);
            $table->float("ptt_price",10,2);
            $table->float("mng_price",10,2);
            $table->float("surat_price",10,2);
            $table->float("sendeo_price",10,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('n11_cargo_prices', function (Blueprint $table) {
            //
            $table->dropColumn('aras_price');
            $table->dropColumn('ptt_price');
            $table->dropColumn('mng_price');
            $table->dropColumn('surat_price');
            $table->dropColumn('sendeo_price');

        });
    }
};
