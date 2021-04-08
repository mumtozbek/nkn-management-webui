<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('host', 30);
            $table->string('status', 30)->nullable();
            $table->string('version', 10)->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('proposals')->nullable();
            $table->unsignedInteger('relays')->nullable();
            $table->unsignedFloat('speed')->nullable();
            $table->unsignedInteger('uptime')->nullable();
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
        Schema::dropIfExists('nodes');
    }
}
