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
                // Update wins and losses for teams
                if ($game->team_1_result === true) {
                    $game->team1->wins += 1;
                    //this calls a function within team model which updates all users accordingly to the result
                    $game->team1->updateUsersStats(true);
                } elseif ($game->team_2_result === true) {
                    $game->team2->wins += 1;
                    $game->team2->updateUsersStats(true);
                }
                elseif($game->team_2_result === false) {
                    $game->team2->losses += 1;
                    $game->team2->updateUsersStats(false);
                }
                elseif($game->team_1_result === false){
                    $game->team1->losses += 1;
                    $game->team1->updateUsersStats(false);
                }
    
                // Update ranks for teams
                if ($game->team_1_result === true) {
                    $game->team1->rank += 2;
                } elseif ($game->team_2_result === true) {
                    $game->team2->rank += 2;
                } elseif ($game->team_1_result === false && $game->team1->rank > 0) {
                    $game->team1->rank -= 1;
                } elseif ($game->team_2_result === false && $game->team2->rank > 0) {
                    $game->team2->rank -= 1;
                }
    
                // Save changes to teams
                $game->team1->save();
                $game->team2->save();
    
                //this code didnt work as there were duplicates 
                // Update wins, losses, and ranks for users associated with each team

                // $game->team1->users->each(function ($user) use ($game) {

                //     // dd($user, $game);
                //     $user->wins += $game->team_1_result ? 1 : 0;
                //     $user->losses += $game->team_1_result ? 0 : 1;
                //     $user->rank += $game->team_1_result ? 2 : ($user->rank > 0 ? -1 : 0);
                //     $user->save();

                // });

    
                // $game->team2->users->each(function ($user) use ($game) {
                    
                //         $user->wins += $game->team_2_result ? 1 : 0;
                //         $user->losses += $game->team_2_result ? 0 : 1;
                //         $user->rank += $game->team_2_result ? 2 : ($user->rank > 0 ? -1 : 0);
                //         $user->save();
                    
                // });
            }
            if ($game->status === 'cancelled'){
                if ($game->team_1_result === true) {
                    $game->team1->wins += 1;
                    //this calls a function within team model which updates all users accordingly to the result
                    $game->team1->updateUsersStats(true);
                } elseif ($game->team_2_result === true) {
                    $game->team2->wins += 1;
                    $game->team2->updateUsersStats(true);
                }
                elseif($game->team_2_result === false) {
                    $game->team2->losses += 1;
                    $game->team2->updateUsersStats(false);
                }
                elseif($game->team_1_result === false){
                    $game->team1->losses += 1;
                    $game->team1->updateUsersStats(false);
                }
    
                // Update ranks for teams
                if ($game->team_1_result === true) {
                    $game->team1->rank += 2;
                } elseif ($game->team_2_result === true) {
                    $game->team2->rank += 2;
                } elseif ($game->team_1_result === false && $game->team1->rank > 0) {
                    $game->team1->rank -= 1;
                } elseif ($game->team_2_result === false && $game->team2->rank > 0) {
                    $game->team2->rank -= 1;
                }
    
                // Save changes to teams
                $game->team1->save();
                $game->team2->save();
            }
        });
    }
}    