<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->integer('state_id');
            $table->integer('area_id');
            $table->integer('distributor_id');
            $table->integer('nsm_id');
            $table->integer('zsm_id');
            $table->integer('rsm_id');
            $table->integer('asm_id');
            $table->integer('sm_id');
            $table->integer('ase_id');
            $table->integer('store_id');
            $table->tinyInteger('status')->comment('1: active, 0: inactive')->default(1);
            $table->tinyInteger('is_deleted')->comment('1: yes, 0: no')->default(1);
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
        Schema::dropIfExists('teams');
    }
}
