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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('supplier_name');
            $table->string('supplier_code')->unique();
            $table->unsignedBigInteger('company_id')->nullable();

            $table->string('email')->nullable();
            $table->string('phone');

            // Bank account details (textarea)
            $table->text('bank')->nullable();

            // NID file path
            $table->string('nid', 255)->nullable();

            // 1 = payable, 2 = receivable
            $table->tinyInteger('type')
                ->comment('1 = payable, 2 = receivable');

            $table->text('voucher_address')->nullable();
            $table->text('address')->nullable();

            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
