<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FriendshipsController;
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

    Route::get('/users', [UserController::class, 'get']);
    Route::put('/users/{userId}', [UserController::class, 'update']);

    Route::put('/users/{userId}/avatar', [UserAvatarController::class, 'update']);
    Route::delete('/users/{userId}/avatar', [UserAvatarController::class, 'delete']);

    Route::put('/users/{userId}/password', [PasswordController::class, 'update']);

    Route::post('/users/{userId}/email-verification/send', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:2,1'); // Не более 2 запросов в минуту
});
// endregion

/*
 * GET /users/{userId}/friends получить друзей пользователя
 * DELETE /users/{userId}/friends/{friendId} удалить друга
 * POST /friend-requests создать запрос в друзья
 * PUT /friend-requests/{requestId} принять запрос в друзья
 * DELETE /friend-requests/{requestId} отклонить(отменить) запрос в друзья
 */

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users/{userId}/friends', [FriendshipsController::class, 'getFriends']);
    Route::get('/users/{userId}/out-requests', [FriendshipsController::class, 'getOutgoing']);
    Route::get('/users/{userId}/in-requests', [FriendshipsController::class, 'getIncoming']);
    Route::post('/friend-requests', [FriendshipsController::class, 'create']);
});