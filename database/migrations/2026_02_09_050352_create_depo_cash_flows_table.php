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
        Schema::create('depo_cash_flows', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('invoice_id')->nullable();
            $table->string('description', 255)->nullable();

            $table->decimal('dr_amount', 15, 2)->nullable();
            $table->decimal('cr_amount', 15, 2)->nullable();
            $table->decimal('balance', 15, 2)->default(0.00);

            $table->unsignedBigInteger('depo_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('voucher_route', 50)->nullable();
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depo_cash_flows');
    }
};
