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
        'team_1_result',
        'team_2_result',
    ];

    // Define the relationship with teams
    public function team1()
    {
        return $this->belongsTo(Team::class, 'team_id_1');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team_id_2');
    }

    // Define the relationship with game stats
    public function gameStats()
    {
        return $this->hasMany(UserTeamGameStats::class);
    }

    // Define the boot method to handle model events
    protected static function booted()
    {
        static::saved(function ($game) {
            if ($game->status === 'finished') {
                $team1 = $game->team1;
                $team2 = $game->team2;

                // Check if the game result has been updated before modifying wins and losses.
                if ($game->isDirty('team_1_result') && $game->team_1_result === true) {
                    $team1->wins += 1;
                    $team2->losses += 1;
                } elseif ($game->isDirty('team_2_result') && $game->team_2_result === true) {
                    $team2->wins += 1;
                    $team1->losses += 1;
                }

                // Update ranks for teams
                if ($game->team_1_result === true) {
                    $team1->rank += 2;
                } elseif ($game->team_2_result === true) {
                    $team2->rank += 2;
                } elseif ($game->team_1_result === false && $team1->rank > 0) {
                    $team1->rank -= 1;
                } elseif ($game->team_2_result === false && $team2->rank > 0) {
                    $team2->rank -= 1;
                }

                $team1->save();
                $team2->save();
                    // Update ranks for users associated with the game
            $game->gameStats->each(function ($stat) use ($game) {
                $user = $stat->user;

                // Check if the user's team is part of the game
                if ($user->team_id === $game->team_id_1 || $user->team_id === $game->team_id_2) {
                    if (($game->team_id_1 === $user->team_id && $game->team_1_result === true) ||
                        ($game->team_id_2 === $user->team_id && $game->team_2_result === true)) {
                        $user->rank += 2; // Increment rank for a win
                    } elseif (($game->team_id_1 === $user->team_id && $game->team_1_result === false) ||
                        ($game->team_id_2 === $user->team_id && $game->team_2_result === false)) {
                        if ($user->rank > 0) {
                            $user->rank -= 1; // Decrement rank for a loss, but keep it above 0
                        }
                    }

                    $user->save();
                    }
                });
            }
        });
    }
}