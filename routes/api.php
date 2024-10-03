<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Authentication;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::put('/user/update', [AuthController::class, 'update'])->middleware('auth:sanctum');

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

    //order routes
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    //reviews routes
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    //coupon routes
    Route::post('/coupons', [CouponController::class, 'store']); 
    Route::put('/coupons/{id}', [CouponController::class, 'update']); 
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy']); 
    Route::get('/coupons', [CouponController::class, 'index']); 
    Route::get('/coupons/{id}', [CouponController::class, 'show']); 
});

//Routes for the customers
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    //cart routes
    Route::get('/cart', [CartController::class, 'show']);                   
    Route::post('/cart/add', [CartController::class, 'addProduct']);        
    Route::put('/cart/update/{productId}', [CartController::class, 'updateProduct']); 
    Route::delete('/cart/remove/{productId}', [CartController::class, 'removeProduct']); 
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);     
    Route::post('/checkout', [CheckoutController::class, 'checkout']);      

    //order routes
    Route::get('/orders', [OrderController::class, 'myOrders']);
    Route::put('/orders/{id}', [OrderController::class, 'cancelOrder']);

    //reviews routes
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);

});

//public routes for unauthenticated users
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
