<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['test']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::post('/users', [UserController::class, 'create']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/test', [TestController::class, 'getUser']);
});



