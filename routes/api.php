<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Authentication;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TagController;
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

//Routes for the admins
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

//Routes for the customers
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {});

//public routes for unauthenticated users
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/categories', [CategoryController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/tags', [TagController::class, 'index']);
