<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function createUser(Request $request)
    {
        try{

            $validateUser = Validator::make($request->all(),[
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'name' => 'required',
            ]);

            if($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Successfully created',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ],200);

        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function loginUser(Request $request)
    {
         try{
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', '=', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Login Success',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
         }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
         }
    }
}
