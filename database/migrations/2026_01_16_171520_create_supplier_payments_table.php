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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name');
            $table->string('account_id');
            $table->string('payment_voucher')->unique();
            $table->string('supplier_code')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('balance', 15)->default(0);
            $table->decimal('paying_amount', 15)->default(0);
            $table->decimal('refund_amount', 15)->default(0);
            $table->tinyInteger('payment_status')->default(1)->comment('1=paid,2=partial,3=advance,4=refund');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
