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
        Schema::create('pazarama_product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pazarama_product_id')->nullable()->constrained('pazarama_products');
            $table->uuid('attributeId');
            $table->uuid('attributeValueId');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pazarama_product_attributes');
    }
};
