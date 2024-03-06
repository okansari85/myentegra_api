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
        Schema::create('hb_cargo_prices', function (Blueprint $table) {
            $table->id();
            $table->string('desi');
            $table->float("aras_price",10,2);
            $table->float("mng_price",10,2);
            $table->float("yk_price",10,2);
            $table->float("surat_price",10,2);
            $table->float("ptt_price",10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hb_cargo_prices');
    }
};
