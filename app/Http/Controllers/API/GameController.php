<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Game;
use App\Models\Team;
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

        $games = Game::with(['team1', 'team2'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $games
        ], 200);
    }
}
