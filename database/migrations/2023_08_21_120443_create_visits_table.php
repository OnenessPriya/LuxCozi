<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('area_id');
            $table->integer('visit_id')->nullable();
            $table->date('start_date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('start_location')->nullable();
            $table->string('start_lat')->nullable();
            $table->string('start_lon')->nullable();
            $table->date('end_date')->nullable();
            $table->string('end_time')->nullable();
            $table->string('end_location')->nullable();
            $table->string('end_lat')->nullable();
            $table->string('end_lon')->nullable();
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
        Schema::dropIfExists('visits');
    }
}
