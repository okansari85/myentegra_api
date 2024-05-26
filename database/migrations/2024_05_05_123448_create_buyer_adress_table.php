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
        Schema::create('buyer_adress', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('adressType');
            $table->string('fullName')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('postalCode')->nullable();
            $table->string('gsm')->nullable();
            $table->string('tcId')->nullable();
            $table->string('taxId')->nullable();
            $table->string('taxHouse')->nullable();
            $table->foreignId('buyer_id')->constrained('buyers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_adress');
    }
};
