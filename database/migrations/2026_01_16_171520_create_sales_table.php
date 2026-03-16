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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sale_voucher')->unique();
            $table->date('sale_date');
            $table->date('delivery_date');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('depo_id');
            $table->unsignedBigInteger('mpo_id')->nullable();
            $table->unsignedBigInteger('chemist_house_id');
            $table->decimal('total', 15)->default(0);
            $table->decimal('discount', 15)->default(0);
            $table->decimal('vat', 15)->default(0);
            $table->decimal('previous_due', 15)->default(0);
            $table->decimal('final_total', 15)->default(0);
            $table->decimal('given_amount', 15)->nullable()->default(0);
            $table->decimal('receivable_amount', 15)->default(0);
            $table->tinyInteger('payment_status')->default(2)->comment('1=paid , 2 = unpaid, 3=partial');
            $table->tinyInteger('order_status')->default(1)->comment('1=pending , 2 = approved, 3=delivered');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
