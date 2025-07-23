<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    // User methods
    public function index(Request $request)
    {
        return response()->json([
            'data' => Auth::user()
        ]);
    }
    public function updateProfile(Request $request)
    {
        $user= Auth::user();
        $request->validate([
            "email"=> "required|email|unique:users,email,{$user->id}",
            "name"=> "required|string|max:255",
        ]);
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            "current_password" => "required|string|min:8|max:12",
            "new_password" => "required|string|min:8|max:12|confirmed",
        ]);
        if (password_verify($request->current_password, $user->password)) {
            $user->password = bcrypt($request->new_password);
            $user->save();
            return response()->json([
                "message" => "Password updated successfully"
            ], 200);
        } else {
            return response()->json([
                "message" => "Current password is incorrect"
            ], 403);
        }
    }

    // Authentication methods
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:12|confirmed',
        ]);
        $createUser = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
        ]);
        $createUser->save();
        return response()->json([
            "message" => "Account created successfully"
        ], 200);
    }
    public function login(Request $request)
    {
        
        $validationRequests = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|max:12'
        ]);
        if (Auth::guard('web')->attempt($validationRequests)) {
            $device = $request->userAgent();
            $token = Auth::user()->createToken($device)->plainTextToken;
            return response()->json([
                "user" => Auth::user(),
                "token" => $token
            ]);
        } else {
            return response()->json(
                "Email or Password is incorrect",
                403
            );
        }
        
    }
    public function logout($token = null)
    {
        $user = Auth::guard('sanctum')->user();
        if (null === $token) {
            $user->currentAccessToken()->delete();
            return response()->json([
                'message' => 'logout successful',
            ],200);
        }
        $personaleToken = PersonalAccessToken::findToken($token);
        if ($user->id === $personaleToken->tokenable_id && get_class($user) === $personaleToken->tokenable_type) {
            $personaleToken->delete();
            return response()->json([
                'message' => 'logout successful',
            ],200);
        }

    }
}
