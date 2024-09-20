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

//login
Route::post('/sanctum/token', [AuthController::class, 'login']);

//logout
Route::post("/sanctum/logout", [AuthController::class, 'logout'])->middleware('auth:sanctum');
