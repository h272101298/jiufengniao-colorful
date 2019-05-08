<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProxyListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * 保存上下线信息
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('proxy_id');//代理id
            $table->unsignedInteger('user_id');//下线id
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
        Schema::dropIfExists('proxy_lists');
    }
}
