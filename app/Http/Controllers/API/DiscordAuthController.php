<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DiscordAuthController extends Controller
{
    // Redirect the user to Discord for authorization
    public function redirectToDiscord()
    {
        $query = http_build_query([
            'client_id' => env('DISCORD_CLIENT_ID'),
            'redirect_uri' => env('DISCORD_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'identify email', 
        ]);

        return redirect('https://discord.com/api/oauth2/authorize?' . $query);
    }

    // Handle the callback from Discord
    public function handleCallback(Request $request)
    {
        $code = $request->input('code');

        $response = Http::asForm()->post('https://discord.com/api/oauth2/token', [
            'client_id' => env('DISCORD_CLIENT_ID'),
            'client_secret' => env('DISCORD_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => env('DISCORD_REDIRECT_URI'),
            'scope' => 'identify email', 
        ]);

        // Handle the response from Discord
        if ($response->successful()) {
            $accessToken = $response['access_token'];
            // You can store the access token in the session or database
            return redirect()->route('home')->with('success', 'Logged in successfully!');
        } else {
            return redirect()->route('login')->with('error', 'Failed to login!');
        }
    }
}
