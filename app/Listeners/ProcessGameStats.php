<?php

namespace App\Listeners;

use App\Events\GameCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UserTeamGameStats;

class ProcessGameStats implements ShouldQueue
{
    public function handle(GameCreated $event)
    {
        $game = $event->game;

        
        foreach ($game->team1->users as $user) {
            $this->createUserGameStats($user->id, $game->id, $game->team1->id);
        }

        foreach ($game->team2->users as $user) {
            $this->createUserGameStats($user->id, $game->id, $game->team2->id);
        }
    }

    private function createUserGameStats($userId, $gameId, $teamId)
    {

        UserTeamGameStats::create([
            'user_id' => $userId,
            'game_id' => $gameId,
            'team_id' => $teamId,
            'kills' => 0, 
            'deaths' => 0,
        ]);
    }
}
