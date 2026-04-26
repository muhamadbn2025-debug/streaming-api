<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieCategoryController;
use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;


// Public routes — tidak perlu token
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes — perlu token
Route::middleware('auth:sanctum')->group(function () {
  Route::post('logout', [AuthController::class, 'logout']);
  Route::apiResource('movies', MovieController::class);
  Route::apiResource('categories', MovieCategoryController::class);
});