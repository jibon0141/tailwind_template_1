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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('father_or_husband_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->tinyInteger('marital_status')
                ->nullable()
                ->comment('1=Single,2=Married,3=Divorced,4=Widow');
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')
                ->nullable()
            ->comment('1=Islam,2=Hinduism,3=Christianity,4=Others');
            $table->text('experience')->nullable();
            $table->string('nid_no')->nullable();
            $table->tinyInteger('blood_group')
                ->nullable()
                ->comment('1=A+, 2=A-, 3=B+, 4=B-, 5=O+, 6=O-, 7=AB+, 8=AB-');
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('designation')->nullable();
            $table->string('branch')->nullable();
            $table->date('application_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
