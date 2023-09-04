<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('sequence_no');
            $table->string('order_no');
            $table->integer('user_id');
            $table->integer('store_id');
            $table->integer('order_placed_by')->nullable()->comment('If ASM then asm_id');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('order_lat')->nullable();
            $table->string('order_lng')->nullable();
            $table->string('order_type')->nullable();
            $table->string('comment')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('final_amount')->nullable();
            $table->tinyInteger('status')->comment('1: active, 0: inactive')->default(1);
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
        Schema::dropIfExists('orders');
    }
}
