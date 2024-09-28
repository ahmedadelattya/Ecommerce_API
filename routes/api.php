<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Authentication;
use App\Http\Controllers\CartController;
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

    //product routes
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    //category routes
    Route::get('/products/categories', [CategoryController::class, 'index']);
    Route::post('/products/categories', [CategoryController::class, 'store']);
    Route::get('/products/categories/{slug}', [CategoryController::class, 'show']);
    Route::put('/products/categories/{slug}', [CategoryController::class, 'update']);
    Route::delete('/products/categories/{slug}', [CategoryController::class, 'destroy']);

    //tag routes
    Route::get('/products/tags', [TagController::class, 'index']);
    Route::post('/products/tags', [TagController::class, 'store']);
    Route::get('/products/tags/{id}', [TagController::class, 'show']);
    Route::put('/products/tags/{id}', [TagController::class, 'update']);
    Route::delete('/products/tags/{id}', [TagController::class, 'destroy']);
});

//Routes for the customers
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    //cart routes
    Route::get('/cart', [CartController::class, 'show']);                   // Show cart
    Route::post('/cart/add', [CartController::class, 'addProduct']);         // Add product to cart
    Route::put('/cart/update/{productId}', [CartController::class, 'updateProduct']); // Update product in cart
    Route::delete('/cart/remove/{productId}', [CartController::class, 'removeProduct']); // Remove product from cart
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);      // Clear all items in the cart
});

//public routes for unauthenticated users
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
