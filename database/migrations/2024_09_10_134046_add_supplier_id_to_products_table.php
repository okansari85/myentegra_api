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
        Schema::table('products', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('supplier_id')->nullable(); // Tedarikçi ID sütunu
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null'); // Foreign key tanımı
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
            $table->dropForeign(['supplier_id']); // Foreign key'i kaldır
            $table->dropColumn('supplier_id'); // Sütunu kaldır
        });
    }
};
