<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTeamGameStatsTable extends Migration
{
    public function up()
    {
        Schema::create('user_team_game_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->integer('kills');
            $table->integer('deaths');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_team_game_stats');
    }
}
