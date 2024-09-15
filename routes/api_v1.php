<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);


Route::post('/users', [UserController::class, 'create']);
Route::get('/users/{userId}', [UserController::class, 'find']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users', [UserController::class, 'get']);
    Route::put('/users/{userId}', [UserController::class, 'update']);

    Route::put('/users/{userId}/avatar', [UserAvatarController::class, 'update']);
    Route::delete('/users/{userId}/avatar', [UserAvatarController::class, 'delete']);
});
