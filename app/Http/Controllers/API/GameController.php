<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Game;
use App\Models\Team;
use App\Http\Resources\Games\GameResource;
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
            'queue_type' => 'required|in:1v1,2v2,3v3,4v4',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $teamId1 = $request->input('team_id_1');
        $queueType = $request->input('queue_type');
    
        $ongoingGame = Game::where(function ($query) use ($teamId1) {
                $query->where('team_id_1', $teamId1)
                      ->orWhere('team_id_2', $teamId1);
            })
            ->whereIn('status', ['pending', 'accepted'])
            ->orderByDesc('created_at')
            ->first();
    
            if ($ongoingGame && !in_array($ongoingGame->status, ['finished', 'cancelled'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are still in a game. Contact Customer Service to resolve the problem'
                ], 400);
            }
            
    
        $pendingGame = Game::whereNull('team_id_2')
                           ->where('status', 'pending')
                           ->orderBy('created_at')
                           ->first();
    
        if ($pendingGame) {
            $pendingGame->team_id_2 = (int) $teamId1;
            $pendingGame->status = 'accepted';
            $pendingGame->save();
            event(new GameCreated($pendingGame));
            return response()->json([
                'status' => 'success',
                'message' => 'Joined a game.',
                'data' => $pendingGame
            ], 200);
        }
    
        $game = new Game();
        $game->team_id_1 = $teamId1;
        $game->queue_type = $queueType;
        $game->status = 'pending';
        $game->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Waiting for opponent to join',
            'data' => new GameResource($game)
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
        
        if (Auth::user()->roles->contains('name', 'admin')) {
            $validator = Validator::make($request->all(), [
                'team_1_score' => 'nullable|integer|min:0',
                'team_2_score' => 'nullable|integer|min:0',
                'team_1_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'team_2_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'team_1_result' => 'nullable|boolean',
                'team_2_result' => 'nullable|boolean'
            ]);
            if ($request->has('team_1_score')) {
                $game->team_1_score = $request->input('team_1_score');
            }
        
            if ($request->has('team_2_score')) {
                $game->team_2_score = $request->input('team_2_score');
            }
        
            if ($request->has('team_1_result')) {
                $game->team_1_result = $request->input('team_1_result');
            }
        
            if ($request->has('team_2_result')) {
                $game->team_2_result = $request->input('team_2_result');
            }
        
            if ($request->hasFile('team_1_image')) {
                $team1Image = $request->file('team_1_image');
                $team1ImageName = 'team_' . $team1->id . '_game_' . $game->id . '_' . time() . '.' . $team1Image->getClientOriginalExtension();
                $team1Image->move(public_path('images'), $team1ImageName);
                $game->team_1_image = $team1ImageName;
            }
        
            if ($request->hasFile('team_2_image')) {
                $team2Image = $request->file('team_2_image');
                $team2ImageName = 'team_' . $team2->id . '_game_' . $game->id . '_' . time() . '.' . $team2Image->getClientOriginalExtension();
                $team2Image->move(public_path('images'), $team2ImageName);
                $game->team_2_image = $team2ImageName;
            }

            $game->status = 'finished';
            $game->save();
        
            return response()->json([
                'status' => 'success',
                'message' => 'Game results updated successfully.',
                'data' => new GameResource($game)
            ], 200);
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
            $game->team_1_image = $team1ImageName; 
        }
    
        if ($request->hasFile('team_2_image') && $user->id === $team2->creator_id) {
            $team2Image = $request->file('team_2_image');
            $team2ImageName = 'team_' . $team2->id . '_game_' . $game->id . '_' . time() . '.' . $team2Image->getClientOriginalExtension();
            $team2Image->move(public_path('images'), $team2ImageName);
            $game->team_2_image = $team2ImageName;
        }

     
        $game->status = 'finished';
        $game->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Game results updated successfully.',
            'data' => new GameResource($game)
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
    
        if (($user->id === $team1->creator_id) || ($user->id === $team2->creator_id)) {

            if ($user->id === $team1->creator_id) {
                $game->team_1_result = false;
                $game->team_2_result = true;
            }
            else if ($user->id === $team2->creator_id) {
                $game->team_2_result = false;
                $game->team_1_result = true;
            }
    
            $game->status = 'cancelled';
            $game->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Game cancelled successfully.',
                'data' => new GameResource($game)
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to cancel the game.',
            ], 403);
        }
    }
    

    
 

   
}
