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
        Schema::table('n11_category_commision', function (Blueprint $table) {
            //
            $table->dropColumn('cat4');
            $table->dropColumn('cat3');
            $table->dropColumn('cat2');
            $table->dropColumn('cat1');
            $table->string('category_name')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('n11_category_commision', function (Blueprint $table) {
            //
        });
    }
};
