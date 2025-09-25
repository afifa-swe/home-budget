<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\CategoriesController;

Route::get('/transactions', [TransactionController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
Route::put('/transactions/{id}', [TransactionController::class, 'update']);
Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

Route::get('/stats/monthly', [StatsController::class, 'monthly']);

Route::get('/categories', [CategoriesController::class, 'index']);
