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
        Schema::create('pazarama_products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('displayName');
            $table->text('description');
            $table->uuid('brandId');
            $table->string('brandName');
            $table->string('code');
            $table->integer('stockCount');
            $table->string('stockCode')->nullable();
            $table->integer('priorityRank');
            $table->decimal('vatRate', 5, 2);
            $table->decimal('listPrice', 10, 2);
            $table->decimal('salePrice', 10, 2);
            $table->integer('installmentCount');
            $table->uuid('categoryId');
            $table->integer('state');
            $table->string('stateDescription');
            $table->boolean('isCatalogProduct')->default(true);
            $table->string('groupCode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pazarama_products');
    }
};
