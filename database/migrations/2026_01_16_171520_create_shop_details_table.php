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
        Schema::create('shop_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('chemist_shop_id');
            $table->string('drug_license_number')->nullable();
            $table->date('drug_license_expire_date')->nullable();
            $table->string('drug_license_image')->nullable();
            $table->string('trade_license')->nullable();
            $table->date('trade_license_expire_date')->nullable();
            $table->string('trade_license_image')->nullable();
            $table->string('tin_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_details');
    }
};
