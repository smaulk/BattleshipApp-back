<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FriendshipsController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameInvitationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserStatisticController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::post('/password/forgot', [PasswordController::class, 'forgot']);
Route::post('/password/reset', [PasswordController::class, 'reset']);

// region Users
Route::post('/users', [UserController::class, 'create']);
Route::get('/users/{userId}', [UserController::class, 'find']);
Route::put('/users/{userId}/email-verification', [EmailVerificationController::class, 'verify']);

Route::get('/users/{userId}/statistic', [UserStatisticController::class, 'get']);
Route::get('/leaderboard', [UserStatisticController::class, 'getLeaderBoard']);

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

// region Friendship
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users/{userId}/friends', [FriendshipsController::class, 'getFriends']);
    Route::get('/users/{userId}/out-requests', [FriendshipsController::class, 'getOutgoing']);
    Route::get('/users/{userId}/in-requests', [FriendshipsController::class, 'getIncoming']);
    Route::post('/friendships', [FriendshipsController::class, 'create']);
    Route::put('/friendships/{friendId}', [FriendshipsController::class, 'accept']);
    Route::delete('/friendships/{friendId}', [FriendshipsController::class, 'delete']);
});
// endregion

// region Gameplay
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users/{userId}/games', [GameController::class, 'get']);

    Route::get('/users/{userId}/out-invites', [GameInvitationController::class, 'getOutgoing']);
    Route::get('/users/{userId}/in-invites', [GameInvitationController::class, 'getIncoming']);
    Route::delete('/users/{userId}/invites', [GameInvitationController::class, 'deleteAll']);
    Route::post('/invites', [GameInvitationController::class, 'create']);
    Route::put('/invites/{friendId}', [GameInvitationController::class, 'accept']);
    Route::delete('/invites/{friendId}', [GameInvitationController::class, 'delete']);

    Route::put('/games/{gameId}', [GameController::class, 'finish']);
    Route::post('/games/{gameId}/send-move-data', [GameController::class, 'sendMoveData']);
    Route::post('/games/{gameId}/send-move-result', [GameController::class, 'sendMoveResult']);
    Route::put('/rooms/{roomId}', [GameController::class, 'start']);

    Route::post('/rooms', [RoomController::class, 'create']);
    Route::post('/rooms/{roomId}/join', [RoomController::class, 'join']);
    Route::post('/room-queue', [RoomController::class, 'startSearch']);
    Route::delete('/room-queue', [RoomController::class, 'stopSearch']);
});
// endregion
