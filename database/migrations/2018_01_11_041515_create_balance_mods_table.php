<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceModsTable extends Migration
{
    public function up()
    {
        Schema::create('balance_mods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('balance_type');
            $table->integer('balance');
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('balance_mods');
    }
}
