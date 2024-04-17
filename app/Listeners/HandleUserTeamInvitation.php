<?php

namespace App\Listeners;

use App\Events\UserInvitedToTeam;
use App\Models\UserTeamRequest;

class HandleUserTeamInvitation
{
    /**
     * Handle the event.
     *
     * @param  UserInvitedToTeam  $event
     * @return void
     */
    public function handle(UserInvitedToTeam $event)
    {
        
        UserTeamRequest::create([
            'user_id' => $event->user->id,
            'team_id' => $event->team->id,
            'status' => 'pending',
        ]);
    }
}
