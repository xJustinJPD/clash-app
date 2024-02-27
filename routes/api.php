<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\GameController;
use App\Http\Controllers\API\UserTeamGameStatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes, must be logged in to view
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user/all', [AuthController::class, 'viewAllUsers']);
    
    // Team routes
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::get('/teams/{id}', [TeamController::class, 'show']);
    Route::put('/teams/{id}', [TeamController::class, 'update']);
    Route::delete('/teams/{id}', [TeamController::class, 'destroy']);
    Route::post('/teams/{id}/join', [TeamController::class, 'joinTeam']);
    
    // New routes for leaving a team, removing a user and adding user
    Route::delete('/teams/{id}/leave', [TeamController::class, 'leaveTeam']);
    Route::delete('/teams/{id}/remove-user/{userId}', [TeamController::class, 'removeUser']);
    Route::post('/teams/{teamId}/invite-user', [TeamController::class, 'inviteUser']);




    //games routes
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/games/{id}', [GameController::class, 'show']);
    Route::post('/games', [GameController::class, 'store']);
    Route::put('/games/{id}', [GameController::class, 'update']);
    Route::put('/games/{id}/cancel', [GameController::class, 'cancel']);

    //user game stats
    Route::get('/stats', [UserTeamGameStatsController::class, 'index']);
    Route::post('/stats', [UserTeamGameStatsController::class, 'store']);
    Route::get('/stats/{id}', [UserTeamGameStatsController::class, 'show']);
    Route::put('/stats/{id}', [UserTeamGameStatsController::class, 'update']);
    Route::delete('/stats/{id}', [UserTeamGameStatsController::class, 'destroy']);
});


