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
        Schema::create('depo_due_collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_voucher');
            $table->unsignedBigInteger('depo_id');
            $table->string('depo_name');
            $table->string('contact')->nullable();
            $table->date('payment_date');
            $table->unsignedBigInteger('depo_account_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->decimal('balance', 15)->default(0);
            $table->decimal('receiving_amount', 15)->default(0);
            $table->tinyInteger('payment_status')->default(2)->comment('1=paid, 2=partial,3=advance');
            $table->tinyInteger('status')->default(2)->comment('1=pending, 2=approved, 3=rejected');
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
        Schema::dropIfExists('depo_due_collections');
    }
};
