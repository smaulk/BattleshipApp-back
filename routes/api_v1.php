<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::post('/password/forgot', [PasswordController::class, 'forgot']);
Route::post('/password/reset', [PasswordController::class, 'reset']);

// region Users
Route::post('/users', [UserController::class, 'create']);
Route::get('/users/{userId}', [UserController::class, 'find']);
Route::put('/users/{userId}/email-verification', [EmailVerificationController::class, 'verify']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('/users/{userId}/email-verification/send', [EmailVerificationController::class, 'resend']);

    Route::get('/users', [UserController::class, 'get']);
    Route::put('/users/{userId}', [UserController::class, 'update']);

    Route::put('/users/{userId}/avatar', [UserAvatarController::class, 'update']);
    Route::delete('/users/{userId}/avatar', [UserAvatarController::class, 'delete']);

    Route::put('/users/{userId}/password', [PasswordController::class, 'update']);
});
// endregion
