<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProxyAmountConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_amount_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('system')->default(0);
            $table->integer('level1')->default(0);
            $table->integer('level2')->default(0);
            $table->integer('level3')->default(0);
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
        Schema::dropIfExists('proxy_amount_configs');
    }
}
