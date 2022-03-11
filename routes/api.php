<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);
Route::apiResource('/posts', PostController::class);


Route::middleware('auth:sanctum')->group(function () {
  Route::get('/profile', [UserController::class, 'profile']);
  Route::post('/logout', [UserController::class, 'logout']);
});
