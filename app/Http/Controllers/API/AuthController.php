<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\UserResource;
use Auth;

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
                'password' => bcrypt($request->password)
            ]);
             
            $defaultRole = Role::where('name', 'customer')->first();
            if ($defaultRole) {
            $user->roles()->attach($defaultRole);
            }




            $token = $user->createToken('api-example-salt')->plainTextToken;

            return response()->json([
                'status' => 'Success',
                'message'=>'Welcome to the community ' . $user->username,
                'token' => $token
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
                'token' => $token
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

        // unset($user["wins"]);
        // unset($user["loses"]);
        // unset($user["kills"]);

        
    //     $user->load('roles:id,name');
    //    $user->roles->makeHidden('pivot');
        
        
        return response()->json([
            'user'=>  new UserResource($user),
        ],200);
    }

    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json([
            'message'=>"logged out succesfully"
        ],200);
    }
}
