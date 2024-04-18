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
public function cancelInvite(Request $request, $userId, $teamId)
{
    try {
        // Find the user by ID
        $userInvited = UserTeamRequest::where('user_id', $userId)
            ->where('team_id', $teamId)
            ->firstOrFail();

        // Check if the authenticated user is the creator of the team
        if ($request->user()->id !== $userInvited->team->creator_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $userInvited->delete();

        return response()->json(['message' => 'Invite canceled successfully.'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to cancel invite.'], 500);
    }
}
public function acceptInvite(Request $request, $teamId)
{
    try {
        $user = $request->user();
        
        // Retrieve the Team by its ID
        $team = Team::findOrFail($teamId);
        
        // All associated team invites with this user
        $invite = UserTeamRequest::where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->first();
        
        // If an invite exists, attach the user to the team
        if ($invite) {
            $team->users()->attach($user->id);
            
            // Delete the invite record to maintain database cleanliness
            $invite->delete();
            
            return response()->json(['message' => 'Invite accepted and joined the team successfully.'], 200);
        } else {
            return response()->json(['message' => 'No pending invitation found for this team.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to accept the invite and join the team.'], 500);
    }
}
public function rejectInvite(Request $request, $teamId)
{
    try {
        $user = $request->user();
        
        // Find the team invitation for the authenticated user and the specified team
        $invite = UserTeamRequest::where('user_id', $user->id)
            ->where('team_id', $teamId)
            ->first();
        
        // If an invitation exists, delete it
        if ($invite) {
            $invite->delete();
            
            return response()->json(['message' => 'Invite rejected successfully.'], 200);
        } else {
            return response()->json(['message' => 'No pending invitation found for this team.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to reject the invite.'], 500);
    }
}
}
