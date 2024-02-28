<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Support\Facades\Validator;
use Auth;

class FriendRequestController extends Controller
{
    public function sendRequest(Request $request, User $user)
    {
        auth()->user()->friends()->attach($user->id, ['status' => 'pending']);
        return response()->json(['message' => 'Friend request sent.'], 200);
    }

    public function viewSentRequests(Request $request)
    {
        try {
            // Retrieve the authenticated user
            $user = $request->user();
    
            // Retrieve all friend requests where the user is the sender (user_id)
            $sentRequests = Friend::where('user_id', $user->id)
                ->where('status', 'pending')
                ->get();
    
            return response()->json([
                'message' => 'Sent friend requests retrieved successfully.',
                'requests' => $sentRequests,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve sent friend requests.'], 500);
        }
    }
    
    public function viewReceivedRequests(Request $request)
    {
        try {
            // Retrieve the authenticated user
            $user = $request->user();
    
            // Retrieve all friend requests where the user is the recipient (friend_id)
            $receivedRequests = Friend::where('friend_id', $user->id)
                ->where('status', 'pending')
                ->get();
    
            return response()->json([
                'message' => 'Received friend requests retrieved successfully.',
                'requests' => $receivedRequests,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve received friend requests.'], 500);
        }
    }


    public function removeFriend(Request $request, $friendId)
{
    try {
        $user = $request->user(); 

        // Detach the friend relationship
        $user->friends()->detach($friendId);

        return response()->json([
            'message' => 'Friend removed successfully.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to remove friend.'], 500);
    }
}
}
