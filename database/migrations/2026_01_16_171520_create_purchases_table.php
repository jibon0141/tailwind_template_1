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
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('purchase_voucher')->unique();
            $table->date('purchase_date');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('depo_id')->index();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->unsignedBigInteger('account_id')->index();
            $table->decimal('total', 15)->default(0);
            $table->decimal('discount', 15)->nullable();
            $table->decimal('vat', 5)->nullable();
            $table->decimal('advance', 15)->nullable();
            $table->decimal('previous_due', 15)->nullable();
            $table->decimal('final_total', 15)->default(0);
            $table->decimal('given_amount', 15)->nullable();
            $table->decimal('payable_amount', 15)->nullable();
            $table->integer('payment_status')
                ->comment('1=paid,2=unpaid,3=partially paid,4=advanced')
                ->nullable();
            $table->integer('purchased_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
