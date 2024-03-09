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
        Schema::create('n11_category_commision', function (Blueprint $table) {
            $table->id();
            $table->string('cat4');
            $table->string('cat3');
            $table->string('cat2');
            $table->string('cat1');
            $table->float('komsiyon_orani',10,2);
            $table->float('pazarlama_hizmet_orani',10,2);
            $table->float('pazaryeri_hizmet_orani',10,2);
            $table->unsignedBigInteger('n11_category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('n11_category_commision');
    }
};
