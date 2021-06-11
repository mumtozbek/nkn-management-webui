<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeAndSpeedColumnsToUptimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uptimes', function (Blueprint $table) {
            $table->unsignedDouble('time_total')->after('speed');
            $table->unsignedDouble('time_connect')->after('time_total');
            $table->unsignedDouble('time_pretransfer')->after('time_connect');

            $table->unsignedDouble('speed_upload')->after('time_pretransfer');
            $table->unsignedDouble('speed_download')->after('speed_upload');
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
            $table->dropColumn([
                'time_total',
                'time_connect',
                'time_pretransfer',
                'speed_upload',
                'speed_download',
            ]);
        });
    }
}
