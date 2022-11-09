<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_records', function (Blueprint $table) {
            $table->id();
            $table->string('visitorDocument', 25);
            $table->string('visitorDocumentType', 5)->nullable();
            $table->string('visitorName');
            $table->string('visitorRelationship')->nullable();
            $table->unsignedBigInteger('family_income_record_id');
            $table->unsignedBigInteger('visitor_type_id');

            $table->foreign('family_income_record_id')->references('id')->on('family_income_records');
            $table->foreign('visitor_type_id')->references('id')->on('visitor_types');

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
        Schema::dropIfExists('visitor_records');
    }
}
