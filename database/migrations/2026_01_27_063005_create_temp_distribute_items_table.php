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
        Schema::create('temp_distribute_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('temp_distribute_id');
            $table->unsignedBigInteger('medicine_id');
            $table->decimal('unit_cost', 10);
            $table->integer('quantity');
            $table->integer('free_quantity')->default(0);
            $table->decimal('sub_total', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_distribute_items');
    }
};
