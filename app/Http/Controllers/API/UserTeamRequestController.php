<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserTeamRequest;
use Illuminate\Http\Request;
use App\Http\Resources\Teams\TeamResource;
use App\Http\Resources\Friends\FriendUserResource;
use App\Models\Team;
class UserTeamRequestController extends Controller
{
 
    public function usersRequests(Request $request)
    {
        try {
          
            $user = $request->user();
            // Retrieve all team requests where the user is the sender (user_id)
            $usersPendingTeams = UserTeamRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->with('team')
                ->get();
            //had to pluck teams and place them into a collection to just display each team
                $teamsResource = TeamResource::collection              ($usersPendingTeams->pluck('team'));
                
            return response()->json([
                'message' => 'retrieved invites successfully.',
                'requests' => $teamsResource,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve invites from teams.'], 500);
        }
    }
   
  
public function teamUsers(Request $request, $teamId)
{
    try {
       
        $team = Team::findOrFail($teamId);

       
        $teamRequests = UserTeamRequest::where('team_id', $team->id)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        $teamUsers = FriendUserResource::collection($teamRequests->pluck('user'));

        return response()->json([
            'message' => 'Retrieved team members successfully.',
            'team_users' => $teamUsers,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to retrieve team members.'], 500);
    }
}
}
