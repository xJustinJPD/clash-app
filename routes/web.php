<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DiscordAuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



// not being used in backend
// Route::get('/auth/discord', [DiscordAuthController::class, 'redirectToDiscord'])->name('discord.redirect');
// Route::get('/auth/discord/callback', [DiscordAuthController::class, 'handleCallback'])->name('discord.callback');



// Route::resource('/admin/books', AdminBookController::class)->middleware(['auth', 'role:admin'])->names('admin.books');