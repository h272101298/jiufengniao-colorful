<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->string('orderSn');
            $table->integer('price');//金额，单位分
            $table->float('origin_price');//单价
            $table->string('type')->default('origin');//类型 origin原始，score积分
            $table->tinyInteger('state')->default(1);//状态，1生成，2支付，3发货，4收货
            $table->unsignedBigInteger('product_id');
            $table->string('picture')->nullable();
            $table->string('notify_id')->nullable();
            $table->unsignedBigInteger('express_id')->default(0);
            $table->string('express_number')->nullable();
            $table->integer('number');
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
