<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id_1',
        'team_id_2',
        'team_1_score',
        'team_2_score',
        'queue_type',
        'status',
    ];

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team_id_1');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team_id_2');
    }
    public function gameStats()
    {
        return $this->hasMany(UserTeamGameStats::class);
    }
    //This is a method provided by Laravel's Eloquent model
    protected static function booted()
    {
        //anonymous function that will be called when a Game is saved.
        static::saved(function ($game) {
            if ($game->status === 'finished') {
                $team1 = $game->team1;
                $team2 = $game->team2;

                if ($game->team_1_score > $game->team_2_score) {
                    $team1->wins += 1;
                    $team1->save();
                    $team2->losses += 1;
                    $team2->save();
                } elseif ($game->team_2_score > $game->team_1_score) {
                    $team2->wins += 1;
                    $team2->save();
                    $team1->losses += 1;
                    $team1->save();
                }
            }
        });
    }
}
