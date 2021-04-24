<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCredentialsColumnsToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('username', 32)->after('name');
            $table->string('password', 32)->after('username');
            $table->unsignedBigInteger('ssh_key_id')->after('password')->nullable();

            $table->foreign('ssh_key_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('password');

            $table->dropForeign(['ssh_key_id']);
            $table->dropColumn('ssh_key_id');
        });
    }
}
