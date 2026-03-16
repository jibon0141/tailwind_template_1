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
        Schema::create('main_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_no', 50)->unique();
            $table->string('account_name', 100);
            $table->bigInteger('user_id');
            $table->bigInteger('company_setting_id');
            $table->decimal('opening_balance', 15)->default(0);
            $table->decimal('balance', 15)->default(0);
            $table->boolean('status')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_accounts');
    }
};
