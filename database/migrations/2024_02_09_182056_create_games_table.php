<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('team_id_1')->unsigned();
            $table->bigInteger('team_id_2')->unsigned()->nullable();
            $table->integer('team_1_score')->nullable();
            $table->integer('team_2_score')->nullable();
            $table->boolean('team_1_result')->nullable();
            $table->boolean('team_2_result')->nullable();
            $table->string('team_1_image')->nullable();
            $table->string('team_2_image')->nullable();
            $table->enum('queue_type', ['1v1', '2v2', '3v3', '4v4']);
            $table->enum('status', ['pending','accepted', 'finished', 'cancelled']);
            $table->foreign('team_id_1')->references('id')->on('teams');
            $table->foreign('team_id_2')->references('id')->on('teams');
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
        Schema::dropIfExists('games');
    }
}
