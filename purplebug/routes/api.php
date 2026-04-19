<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout']);

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'add']);
    Route::put('/cart/{productId}', [CartController::class, 'update']);
    Route::delete('/cart/{productId}', [CartController::class, 'remove']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Orders Routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/checkout', [OrderController::class, 'checkout']);
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // Admin Only
    Route::middleware('admin')->group(function () {
        Route::apiResource('/users', UserController::class);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::get('/admin/orders', [App\Http\Controllers\Api\OrderController::class, 'adminIndex']);
        Route::put('/admin/orders/{order}/status', [App\Http\Controllers\Api\OrderController::class, 'updateStatus']);
        Route::delete('/admin/orders/{order}', [App\Http\Controllers\Api\OrderController::class, 'destroy']);
    });
});
