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
        Schema::create('chemist_house_due_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('chemist_house_id');
            $table->string('chemist_house_name');
            $table->string('payment_voucher')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('contact')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->decimal('balance', 15)->default(0);
            $table->decimal('receiving_amount', 15)->default(0);
            $table->tinyInteger('payment_status')->default(0)->comment('1=paid, 2=partial,3=advance');
            $table->string('document')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemist_house_due_payments');
    }
};
