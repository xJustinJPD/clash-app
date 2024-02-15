<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTeamGameStats extends Model
{
    protected $table = 'user_team_game_stats';

    protected $fillable = [
        'user_id', 'game_id', 'team_id', 'kills', 'deaths',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

