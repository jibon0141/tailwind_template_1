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
        Schema::create('medicines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('medicine_name');
            $table->bigInteger('generic_name_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('medicine_category_id');
            $table->unsignedBigInteger('medicine_dosage_form_id');
            $table->string('strength_name');
            $table->decimal('mrp', 10);
            $table->unsignedBigInteger('purchase_percentage');
            $table->decimal('purchase_price', 10);
            $table->unsignedBigInteger('sale_percentage');
            $table->decimal('sale_price', 10);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
