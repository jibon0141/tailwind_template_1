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
        Schema::create('investor_invests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investor_id');
            $table->string('investor_name');
            $table->string('invest_voucher')->unique();
            $table->string('investor_code')->nullable();

            $table->date('payment_date')->nullable();
            $table->string('phone')->nullable();

            $table->unsignedBigInteger('account_id')->nullable();

            $table->decimal('invest_amount', 15, 2)->default(0);
            $table->decimal('investing_amount', 15, 2)->default(0);

            $table->tinyInteger('payment_status')->default(0)
                ->comment('1 = invest, 1 = withdraw');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_invests');
    }
};
