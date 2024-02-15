<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\UserTeamGameStats;
use App\Http\Resources\UserTeamGameStatsResource;

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
    
        return response()->json([
            'status' => 'success',
            'message' => 'UserTeamGameStats updated successfully.',
            'data' => $userTeamGameStats
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
