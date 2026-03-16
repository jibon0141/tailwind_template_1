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
        Schema::create('investor_ledgers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('investor_id');
            $table->date('date');
            $table->string('invoice_id')->nullable();
            $table->string('purpose')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->string('voucher_route')->nullable();
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->tinyInteger('status')->default(1)
                  ->comment('1=invest,2=withdraw');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_ledgers');
    }
};
