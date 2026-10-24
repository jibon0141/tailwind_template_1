<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id')->nullable()->unique();
            $table->string('name');
            $table->string('phone')->nullable()->unique();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->nullable();
            $table->string('user_type');
            $table->string('ref_id')->nullable()->comment('if 0 then it is first generation and have no parent.');
            $table->tinyInteger('designation_id')->nullable()
                ->comment('1 = Associate Partner,2 = Property Partner, 3 = Manager, 4 = General Manager, 5 = Deputy Director, 6 = Director')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 = inactive,1 = active');
            $table->rememberToken();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
