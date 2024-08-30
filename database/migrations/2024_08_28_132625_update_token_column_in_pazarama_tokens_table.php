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
        Schema::table('pazarama_tokens', function (Blueprint $table) {
            //
            $table->text('token')->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pazarama_tokens', function (Blueprint $table) {
            //
            $table->string('token')->change();
        });
    }
};
