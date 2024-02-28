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


    public function removeFriend(Request $request, $id)
    {
        try {
            $user = $request->user();
    
            // Find the friendship record
            $friendship = Friend::where(function ($query) use ($id, $user) {
                $query->where('user_id', $user->id)
                    ->where('friend_id', $id);
            })->orWhere(function ($query) use ($id, $user) {
                $query->where('user_id', $id)
                    ->where('friend_id', $user->id);
            })->first();
    
            if (!$friendship) {
                return response()->json(['message' => 'Friendship not found.'], 404);
            }
    
            // Detach both users from the friendship
            $user->friends()->detach($id);
            $user->friends()->detach($user->id);
    
            // Delete the friendship record
            $friendship->delete();
    
            return response()->json(['message' => 'Friendship removed successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to remove friendship.'], 500);
        }
    }
    
}