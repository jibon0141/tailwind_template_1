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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('medicine_id');
            $table->decimal('mrp', 10)->default(0.00);
            $table->decimal('medicine_discount', 10)->comment('Calculate in %');
            $table->decimal('unit_cost', 10)->default(0.00);
            $table->integer('quantity');
            $table->integer('free_quantity')->default(0);
            $table->date('expire_date');
            $table->decimal('sub_total', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
