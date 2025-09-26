<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;

Route::middleware('auth:api')->group(function () {
	Route::get('/transactions', [TransactionController::class, 'index']);
	Route::post('/transactions', [TransactionController::class, 'store']);
	Route::put('/transactions/{id}', [TransactionController::class, 'update']);
	Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

	// Categories CRUD
	Route::get('/categories', [CategoryController::class, 'index']);
	Route::post('/categories', [CategoryController::class, 'store']);
	Route::put('/categories/{id}', [CategoryController::class, 'update']);
	Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

Route::get('/stats/monthly', [StatsController::class, 'monthly'])->middleware('auth:api');

// Public auth endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:api');

// (categories routes are protected under the auth:api group above)
