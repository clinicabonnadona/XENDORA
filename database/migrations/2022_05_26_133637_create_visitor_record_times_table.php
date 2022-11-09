<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorRecordTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_record_times', function (Blueprint $table) {
            $table->id();
            $table->dateTime('visitorEntryDate');
            $table->dateTime('visitorOutDate')->nullable();
            $table->integer('visitorHoursDuration')->nullable();
            $table->integer('patientAdmConsecutive');

            $table->unsignedBigInteger('family_income_record_id');
            $table->unsignedBigInteger('visitor_record_id');

            $table->foreign('family_income_record_id')->references('id')->on('family_income_records');
            $table->foreign('visitor_record_id')->references('id')->on('visitor_records');
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
        Schema::dropIfExists('visitor_record_times');
    }
}
