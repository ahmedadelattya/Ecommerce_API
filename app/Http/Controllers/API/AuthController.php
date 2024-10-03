<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer'
        ]);

        // Create a cart for the user
        $user->cart()->create();

        // Generate token for the registered user
        $token = $user->createToken($request->device_name ?? 'default_device')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 422);
        }
        # limit creation no of token
        if ($user->tokens()->count() < 3) {
            return $user->createToken($request->device_name)->plainTextToken;
        }
        return response()->json(['message' => 'Maximum number of sessions reached. Please logout from one device to continue.'], 422);
    }


    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function update(Request $request)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Get the authenticated user
        $user = Auth::user();

       // Update user information
        $user->name = $request->name;
        $user->email = $request->email;

        // Only update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();


        return response()->json([
            'message' => 'User information updated successfully!',
            'user' => $user,
        ]);
    }

    public function destroy(Request $request)
    {
    $user = Auth::user();

    // Delete the user account
    $user->delete();

    // Revoke the user's token (log out the user)
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Account deleted successfully!'], 200);
    }
}
