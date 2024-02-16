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
    public function teamCreator()
{
    $team = $this->team;

  
    if ($team === null || $team->creator_id === null) {
        return null;
    }
    
 
    return $team->creator_id;
}
protected static function booted()
    {
        static::saved(function ($userTeamGameStats) {
            $user = $userTeamGameStats->user;
            $user->kills = $user->gameStats->sum('kills');
            $user->deaths = $user->gameStats->sum('deaths');
            $user->save();
        });
    }
    

}

