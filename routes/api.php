<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Authentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

####### Authenticate APIs
// Registration
Route::post('/register', [AuthController::class, 'register']);

// Login (token creation)
Route::post('/sanctum/token', [AuthController::class, 'login']);

// Logout (token revocation)
Route::post('/sanctum/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
