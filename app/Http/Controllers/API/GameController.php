<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Game;
use App\Models\Team;
use App\Http\Resources\GameResource;
use App\Events\GameCreated;
use Auth;

class GameController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not logged in.'
            ], 401);
        }

        // $games = Game::with(['team1', 'team2'])->get();
        return response()->json([
            'status' => 'success',
            'data' => GameResource::collection(Game::all())
        ], 200);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id_1' => 'required|exists:teams,id',
            'team_id_2' => 'required|exists:teams,id',
            'queue_type' => 'required|in:1v1,2v2,3v3,4v4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors()
            ], 422);
        }

        $game = new Game();
        $game->team_id_1 = $request->input('team_id_1');
        $game->queue_type = $request->input('queue_type');
        $game->team_id_2 = $request->input('team_id_2');
        $game->save();
        event(new GameCreated($game));

        return response()->json([
            'status' => 'success',
            'message' => 'Game created successfully.',
            'data' => $game
        ], 201);
    }
    public function show($id)
    {
        $game = Game::find($id);

        if ($game === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Game not found.',
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
            'data' => new GameResource($game),
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);
    
        if ($game === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Game not found.',
            ], 404);
        }
    
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not logged in.'
            ], 401);
        }
        
        $user = Auth::user();
        $team1 = $game->team1;
        $team2 = $game->team2;
    
        if ($user->roles->contains('name', 'admin') || ($user->id === $team1->creator_id) || ($user->id === $team2->creator_id)) {
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update the game results.',
            ], 403);
        }
        
    
       
        $validator = Validator::make($request->all(), [
            'team_1_score' => 'nullable|integer|min:0',
            'team_2_score' => 'nullable|integer|min:0',
            'team_1_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'team_2_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'team_1_result' => 'nullable|boolean',
            'team_2_result' => 'nullable|boolean',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors()
            ], 422);
        }
    
       
        if ($request->has('team_1_score') && $user->id === $team1->creator_id) {
            $game->team_1_score = $request->input('team_1_score');
        }
    
        if ($request->has('team_2_score') && $user->id === $team2->creator_id) {
            $game->team_2_score = $request->input('team_2_score');
        }
    
        if ($request->has('team_1_result') && $user->id === $team1->creator_id) {
            $game->team_1_result = $request->input('team_1_result');
        }
    
        if ($request->has('team_2_result') && $user->id === $team2->creator_id) {
            $game->team_2_result = $request->input('team_2_result');
        }
    
        
        if ($request->hasFile('team_1_image') && $user->id === $team1->creator_id) {
            $team1Image = $request->file('team_1_image');
            $team1ImageName = 'team_' . $team1->id . '_game_' . $game->id . '_' . time() . '.' . $team1Image->getClientOriginalExtension();
            $team1Image->move(public_path('images'), $team1ImageName);
            $team1->image = $team1ImageName;
            $team1->save();
        }
    
        if ($request->hasFile('team_2_image') && $user->id === $team2->creator_id) {
            $team2Image = $request->file('team_2_image');
            $team2ImageName = 'team_' . $team2->id . '_game_' . $game->id . '_' . time() . '.' . $team2Image->getClientOriginalExtension();
            $team2Image->move(public_path('images'), $team2ImageName);
            $team2->image = $team2ImageName;
            $team2->save();
        }
        $game->status = 'finished';
        $game->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Game results updated successfully.',
            'data' => $game
        ], 200);

    }

    public function cancel(Request $request, $id)
    {
        $game = Game::findOrFail($id);
    
        if ($game === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Game not found.',
            ], 404);
        }
    
        $user = Auth::user();
        $team1 = $game->team1;
        $team2 = $game->team2;
    
        if ($user->roles->contains('name', 'admin') || ($user->id === $team1->creator_id) || ($user->id === $team2->creator_id)) {
            if ($user->id === $team1->creator_id) {
                $game->team_1_result = false;
                $game->team_2_result = true;
            }
            if ($user->id === $team2->creator_id) {
                $game->team_2_result = false;
                $game->team_1_result = true;
            }
    
            $game->status = 'cancelled';
            $game->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Game cancelled successfully.',
                'data' => $game
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to cancel the game.',
            ], 403);
        }
    }
    

    
 

   
}
