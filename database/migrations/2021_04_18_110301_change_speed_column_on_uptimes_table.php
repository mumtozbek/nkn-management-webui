<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSpeedColumnOnUptimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uptimes', function (Blueprint $table) {
            $table->unsignedFloat('speed', 16)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uptimes', function (Blueprint $table) {
            $table->unsignedFloat('speed')->change();
        });
    }
}
