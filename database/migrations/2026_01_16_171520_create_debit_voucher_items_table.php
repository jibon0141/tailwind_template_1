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
        Schema::create('debit_voucher_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('debit_voucher_id');
            $table->unsignedBigInteger('chart_of_account_id');
            $table->text('description')->nullable();
            $table->decimal('paid_amount', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_voucher_items');
    }
};
