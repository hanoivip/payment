<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModBalancesTable extends Migration
{
    public function up()
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->float('balance');
        });
            Schema::table('balance_mods', function (Blueprint $table) {
            $table->float('balance');
        });
    }

    public function down()
    {
        //
    }
}
