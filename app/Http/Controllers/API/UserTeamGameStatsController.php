<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\UserTeamGameStats;
use App\Http\Resources\Stats\UserTeamGameStatsResource;
use App\Http\Resources\Stats\RelevantUserStatResource;
use App\Models\Game;
use App\Models\Team;
use App\Models\User;


class UserTeamGameStatsController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not logged in.'
            ], 401);
        }

        $userTeamGameStats = UserTeamGameStats::all();

        return response()->json([
            'status' => 'success',
            'data' => UserTeamGameStatsResource::collection($userTeamGameStats),
        ], 200);
    }

    // public function store(Request $request)
    // {
        
    // }

    public function show($id)
    {
        $stat = UserTeamGameStats::find($id);

        if ($stat === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'stat not found.',
            ], 404);
        }
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not logged in.'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => new UserTeamGameStatsResource($stat),
        ], 200);

    }
    public function getRelevantUsers(Request $request, $gameId, $teamId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not logged in.'
            ], 401);
        }
    
        // Fetch the game
        $game = Game::find($gameId);
        if ($game === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Game not found.'
            ], 404);
        }
    
        // Fetch the team
        $team = Team::find($teamId);
        if ($team === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found.'
            ], 404);
        }
    
        if (!$user->roles->contains('name', 'admin') && $user->id !== $team->creator_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view the users for this game and team.'
            ], 403);
        }
    
       
        $userStats = UserTeamGameStats::where('game_id', $gameId)
    ->where('team_id', $teamId)
    ->with('user') // Load the user information
    ->get();

// Check if any user statistics were retrieved
if ($userStats->isEmpty()) {
    return response()->json([
        'status' => 'error',
        'message' => 'No user statistics found for this game and team.',
    ], 404);
}

// Initialize an array to store formatted statistics
$formattedStats = [];

// Loop through each user statistic
foreach ($userStats as $userStat) {
    // Transform the user statistic into the appropriate resource format
    $formattedStat = new RelevantUserStatResource($userStat);
    
    // Add the formatted statistic to the array
    $formattedStats[] = $formattedStat;
}

return response()->json([
    'status' => 'success',
    'data' => $formattedStats,
], 200);
    }
    public function update(Request $request, $id)
    {
        $userTeamGameStats = UserTeamGameStats::findOrFail($id);
    
        if ($userTeamGameStats === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stats not found.',
            ], 404);
        }
    
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not logged in.'
            ], 401);
        }
    
        $user = Auth::user();
        $team = $userTeamGameStats->team;
        $game = $userTeamGameStats->game;
    
       
        if ($user->roles->contains('name', 'admin')) {
         
        } else {
            
            if ($user->id !== $userTeamGameStats->teamCreator()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to update the stats.',
                ], 403);
            }
        }
    
        
        $validator = Validator::make($request->all(), [
            'kills' => 'nullable|integer|min:0',
            'deaths' => 'nullable|integer|min:0',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors()
            ], 422);
        }
    
       
        if ($request->has('kills')) {
            $userTeamGameStats->kills = $request->input('kills');
        }
    
        if ($request->has('deaths')) {
            $userTeamGameStats->deaths = $request->input('deaths');
        }
    
       
        $userTeamGameStats->save();
        $user->updateUserStatsForGames();
        return response()->json([
            'status' => 'success',
            'message' => 'UserTeamGameStats updated successfully.',
            'data' => new UserTeamGameStatsResource($userTeamGameStats)
        ], 200);
    }
    

    
    

    public function destroy($id)
    {
        $stat = UserTeamGameStats::findOrFail($id);
    
        if ($stat === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found.',
            ], 404);
        }
    
        $user = Auth::user();
        
        if (Auth::user()->roles->contains('name', 'admin')) {
            $stat->delete();
            return response()->json([
                'status' => 'success',
                'message' => "Statistics: {$id} deleted"
            ], 200);
        }
        if (!Auth::user()->roles->contains('name', 'admin')) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        }
    }
}
