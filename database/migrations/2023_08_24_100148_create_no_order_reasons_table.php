<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoOrderReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('no_order_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('noorderreason')->nullable();
            $table->timestamps();
        });
        DB::table('no_order_reasons')->insert(
            array(
                'noorderreason' => 'Product related issue',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('no_order_reasons');
    }
}
