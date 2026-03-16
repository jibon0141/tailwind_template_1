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
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('investor_code');
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->string('nid')->nullable();
            $table->string('nid_front')->nullable();
            $table->string('nid_back')->nullable();
            $table->text('bank_details')->nullable();
            $table->text('address')->nullable();
            $table->decimal('invest_amount', 15, 2)->default(0);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->string('nominee_name')->nullable();
            $table->string('nominee_relation')->nullable();
            $table->text('nominee_address')->nullable();
            $table->string('nominee_contact')->nullable();
            $table->text('nominee_bank_details')->nullable();
            $table->string('nominee_nid')->nullable();
            $table->string('nominee_nid_front')->nullable();
            $table->string('nominee_nid_back')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=Inactive,1=Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
