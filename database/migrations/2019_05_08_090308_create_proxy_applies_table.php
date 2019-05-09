<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProxyAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_applies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');//微信用户id
            $table->string('phone');//电话
            $table->string('name');//姓名
//            $table->tinyInteger('sex')->default(1);//1 man ,2 female
            $table->string('bank');//开户银行
            $table->string('account');//银行账号
            $table->tinyInteger('state')->default(1);//默认1，通过2，拒绝3
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
        Schema::dropIfExists('proxy_applies');
    }
}
