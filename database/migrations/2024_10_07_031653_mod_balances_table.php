<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModBalancesTable extends Migration
{
    public function up()
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->float('balance')->change();
        });
            Schema::table('balance_mods', function (Blueprint $table) {
            $table->float('balance')->change();
        });
    }

    public function down()
    {
        //
    }
}
