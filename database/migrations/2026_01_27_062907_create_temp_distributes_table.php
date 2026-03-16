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
        Schema::create('temp_distributes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('distribute_voucher')->nullable();
            $table->date('distribute_date');
            $table->unsignedBigInteger('depo_id');
            $table->decimal('total', 15)->default(0);
            $table->decimal('discount', 15)->default(0);
            $table->decimal('vat', 15)->default(0);
            $table->decimal('advance', 15)->default(0);
            $table->decimal('previous_due', 15)->default(0);
            $table->decimal('final_total', 15)->default(0);
            $table->decimal('receivable_amount', 15)->default(0);
            $table->tinyInteger('payment_status')->comment('1=paid , 2 = unpaid, 3=partial');
            $table->tinyInteger('order_status')->comment('1=pending , 2 = approved, 3=delivered,4=rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_distributes');
    }
};
