<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceRequests extends Migration
{
    public function up()
    {
        Schema::create('balance_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('gm_id');
            $table->string('target_id');
            $table->string('reason');
            $table->double('amount');
            $table->tinyInteger('balance_type')->default(0);
            $table->string('currency')->nullable();
            $table->tinyInteger('status');
            $table->integer('approve_id')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('balance_requests');
    }
}
