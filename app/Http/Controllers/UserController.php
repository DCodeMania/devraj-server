<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {
    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validationError' => true,
                'message' => $validator->errors()
            ], 200);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'error' => false,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
            ]
        ], 200);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validationError' => true,
                'message' => $validator->errors()
            ], 200);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'credError' => true,
                'message' => 'Invalid credentials'
            ], 200);
        }
        $user = User::where('email', $request->email)->first();
        return response()->json([
            'error' => false,
            'message' => 'User loggedIn successfully',
            'data' => [
                'user' => Auth::user(),
                'token' => $user->createToken('API Token')->plainTextToken
            ]
        ], 200);
    }

    public function profile() {
        return response()->json([
            'error' => false,
            'message' => 'User profile',
            'user' => Auth::user()
        ], 200);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json([
            'error' => false,
            'message' => 'User loggedOut successfully'
        ], 200);
    }
}
