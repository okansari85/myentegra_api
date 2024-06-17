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
        Schema::create('malzemos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('raf_id')->constrained('depos');
            $table->foreignId('depo_id')->constrained('depos');
            $table->string("productCode");
            $table->string("productDesc");
            $table->integer("stock");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('malzemos');
    }
};
