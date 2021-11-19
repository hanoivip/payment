<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebtopupHistory extends Migration
{
    public function up()
    {
        Schema::create('webtopup_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('trans_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('webtopup_logs');
    }
}
