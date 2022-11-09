<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilyIncomeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('family_income_records', function (Blueprint $table) {
            $table->id();
            $table->string('patientName');
            $table->string('patientDocument');
            $table->string('patientDocumentType', 5);
            $table->integer('patientAdmConsecutive');
            $table->dateTime('patientAdmDate');
            $table->string('patientHabitation', 10);
            $table->dateTime('patientOutDate')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('family_income_records');
    }
}
