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
            $table->boolean('is_checked')->default(0); // Boolean sütun, varsayılan değer 0
            $table->unsignedInteger('checked_quantity')->default(0); // Integer sütun, varsayılan değer 0
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            //
            $table->dropColumn('is_checked'); // Sütunu geri al
            $table->dropColumn('checked_quantity'); // Sütunu geri al
        });
    }
};
