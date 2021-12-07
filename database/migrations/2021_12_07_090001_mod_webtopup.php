<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModWebtopup extends Migration
{
    public function up()
    {
        Schema::table('webtopup_logs', function (Blueprint $table) {
            $table->boolean('callback')->default(false);
            $table->boolean('by_admin')->default(false);
        });
    }

    public function down()
    {
        Schema::table('webtopup_logs', function (Blueprint $table) {
            $table->dropColumn('callback');
            $table->dropColumn('by_admin');
        });
    }
}
