<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * 微信用户个人信息
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->commit('WeChatUserId');
            $table->string('name')->commit('Name');
            $table->tinyInteger('sex')->commit('sex');
//            $table->string('birthday')->commit('birthday');
            $table->string('phone')->commit('phone');
//            $table->text('desc')->nullable()->commit('description');
//            $table->
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
        Schema::dropIfExists('user_infos');
    }
}
