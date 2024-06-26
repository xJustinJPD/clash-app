<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\Friend;
use App\Http\Resources\UserResource;
use Auth;
use Storage;

class AuthController extends Controller
{
    public function register(Request $request){
        try{
            $validator = Validator::make($request->all(),
            [
                'username' => 'required|min:3|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);


            if ($validator->fails()) {
                
                return response()->json(
                [
                    'status' => 'Error',
                    'message' => 'validation error',
                    'errors' => $validator->errors()
                ],
                422);
            }

           
            $user = User::create(
            [
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'image' => 'images/no_image_available.jpg',
            ]);
             
            $defaultRole = Role::where('name', 'customer')->first();
            
            $user->roles()->attach($defaultRole);
            




            $token = $user->createToken('api-example-salt')->plainTextToken;

            return response()->json([
                'status' => 'Success',
                'message'=>'Welcome to the community ' . $user->username,
                'token' => $token,
                'id' => $user->id,
                'role' => $user->roles->pluck('name')
            ],200);
        }
        catch(\Throwable $th){
            return response()->json([
                'status' => 'Error',
                'message'=> $th->getMessage()
            ], 500);
        }
    }
    public function login(Request $request){
        try{
            $validateUser = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => 'Error',
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('api-example-salt')->plainTextToken;

            

            return response()->json([
                'status' => 'Success',
                'message'=>'Welcome back ' . $user->username,
                'token' => $token,
                'id' => $user->id,
                'role' => $user->roles->pluck('name')
            ],200);
        }
        catch(\Throwable $th){
            return response()->json([
                'status' => 'Error',
                'message'=> $th->getMessage()
            ], 500);
        }
    }

    public function user(){


        $user = Auth::user();
        // $user->updateWinsAndLossesFromTeams();
        // unset($user["wins"]);
        // unset($user["loses"]);
        // unset($user["kills"]);

        
    //     $user->load('roles:id,name');
    //    $user->roles->makeHidden('pivot');
        
        
        return response()->json([
            'user'=>  new UserResource($user),
        ],200);
    }

    public function showUser($id)
    {   
        $user = User::find($id);
        
        if ($user === null) {
            $statusMsg = 'User not found!';
            $statusCode = 404;
        } else {
            // Load the teams related to the user
            $user->load('teams');
            
            $statusMsg = 'success';
            $statusCode = 200;
        }
    
        return response()->json([
            'status' => $statusMsg,
            'data' =>  new UserResource($user),
        ], $statusCode);
    }
    

    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json([
            'message'=>"logged out succesfully"
        ],200);
    }

    
    public function viewAllUsers()
    {
        try {
            $authUserId = Auth::id();
    
            $users = User::where('id', '!=', $authUserId)->get();
            // foreach ($users as $user) {
            //     $user->updateWinsAndLossesFromTeams();
            // }
            return response()->json([
                'status' => 'Success',
                'users' => UserResource::collection($users)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
public function acceptRequest(Request $request, $requestId)
{
    try {
        $friendRequest = Friend::findOrFail($requestId);

        if ($friendRequest->friend_id !== auth()->id()) {
            return response()->json(['message' => 'You are not authorized to accept this friend request.'], 403);
        }

        $friendRequest->update(['status' => 'accepted']);

        return response()->json(['message' => 'Friend request accepted.'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to accept friend request.'], 500);
    }
}

public function rejectRequest(Request $request, $requestId)
{
    try {
        $friendRequest = Friend::findOrFail($requestId);

        // Check if the authenticated user is the recipient of the friend request
        if ($friendRequest->friend_id !== auth()->id()) {
            return response()->json(['message' => 'You are not authorized to reject this friend request.'], 403);
        }

        $friendRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Friend request rejected.'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to reject friend request.'], 500);
    }
}

public function updateUser(Request $request, $id)
{   
    $authUserId = Auth::id();

    $user = User::findOrFail($id);

    if ($user === null) {
        return response()->json([
            'status' => 'User not found!',
            'data' => null
        ], 404);
    }

    if ($authUserId != $id) {
        return response()->json([
            'status' => 'Error',
            'message' => 'You are not authorized to perform this action.'
        ], 403);
    }

    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:50|unique:users,username,'.$id,
        'email' => 'required|email|max:255|unique:users,email,'.$id,
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'description' => 'nullable|string|max:250' 
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'Error: see below',
            'errors' => $validator->errors()
        ], 422);
    }
    $imageFormal = $user->image;
    if ($request->hasFile('imageFile')) {
        $image = $request->file('imageFile');
        if(env('IMAGE_ENGINE') == 's3'){
            if($imageFormal != null){
                Storage::disk('s3')->delete('images/'. $imageFormal);
            }
            $imageName = Storage::disk('s3')->put('images', $image);
            
        }
        else{
            if($imageFormal != null){
                unlink(public_path('images/' . $imageFormal));
                }
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        }
        $user->image = $imageName; 
    }

    if ($request->username !== null) {
        $user->username = $request->input('username');
    }
   
    $user->email = $request->input('email');
    $user->description = $request->input('description');
    $user->save();

    return response()->json([
        'status' => 'success',
        'data' => new UserResource($user)
    ], 200);
}
public function registerAdmin(Request $request)
{
    try {
        // Check if the authenticated user is an admin
        if (!Auth::user()->roles->contains('name', 'admin')) {
            return response()->json(['message' => 'Unauthorized. Only admins can perform this action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|min:3|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // Attach the admin role to the user
        $adminRole = Role::where('name', 'admin')->first();
        $user->roles()->attach($adminRole);

        $token = $user->createToken('api-example-salt')->plainTextToken;

        return response()->json([
            'status' => 'Success',
            'message' => 'Admin user created successfully.',
            'id' => $user->id,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'Error',
            'message' => 'Failed to create admin user.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function updatePassword(Request $request, $id)
{
    $authUserId = Auth::id();

    $user = User::findOrFail($id);

    if ($user === null) {
        return response()->json([
            'status' => 'Error',
            'message' => 'User not found!',
        ], 404);
    }

    if ($authUserId != $id) {
        return response()->json([
            'status' => 'Error',
            'message' => 'You are not authorized to perform this action.'
        ], 403);
    }

    $validator = Validator::make($request->all(), [
        'password' => 'required|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'Error',
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    $user->password = bcrypt($request->input('password'));
    $user->save();

    return response()->json([
        'status' => 'Success',
        'message' => 'Password updated successfully.'
    ], 200);
}



}