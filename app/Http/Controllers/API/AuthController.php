<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
        return response()->json(['message' => 'Maximum accounts reached please logout from one of them'], 422);
    }


    public function logout () {
        $user = Auth::user();
        # remove current token
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
