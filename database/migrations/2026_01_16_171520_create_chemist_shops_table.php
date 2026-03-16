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
        Schema::create('chemist_shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('shop_name');
            $table->string('owner_name');
            $table->unsignedBigInteger('shop_type_id');
            $table->unsignedBigInteger('depo_id');
            $table->unsignedBigInteger('mpo_id');
            $table->boolean('status')->default(true);
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('contact')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemist_shops');
    }
};
