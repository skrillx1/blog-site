<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|in:admin,editor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // The mutator in the model will hash this
            'role' => $request->role ?? 'editor',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        \Log::info('=== LOGIN ATTEMPT START ===');
        \Log::info('Request data:', $request->all());

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Find user first
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            \Log::error('User not found with email: ' . $request->email);
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        \Log::info('User found:', ['id' => $user->id, 'email' => $user->email]);
        
        // Manual password check
        $passwordMatch = Hash::check($request->password, $user->password);
        \Log::info('Manual password check result: ' . ($passwordMatch ? 'MATCH' : 'NO MATCH'));
        
        // Alternative: Try Auth::attempt with credentials array
        $credentials = $request->only('email', 'password');
        $authAttempt = Auth::attempt($credentials);
        \Log::info('Auth::attempt result: ' . ($authAttempt ? 'SUCCESS' : 'FAILED'));

        if (!$authAttempt) {
            \Log::error('Authentication failed for user ID: ' . $user->id);
            \Log::error('Input password: ' . $request->password);
            \Log::error('Stored hash: ' . $user->password);
            
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        \Log::info('Login successful, token generated for user ID: ' . $user->id);
        \Log::info('=== LOGIN ATTEMPT END ===');

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}