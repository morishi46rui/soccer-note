<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/health', [HealthController::class, 'index']);

    // Sanctum認証が必要なエンドポイント
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/admin/stats', [AdminController::class, 'getStats']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('/auth/password', [AuthController::class, 'updatePassword']);
        Route::get('/groups', [GroupController::class, 'index']);
        Route::post('/groups', [GroupController::class, 'store']);
        Route::get('/groups/{sqid}', [GroupController::class, 'show']);
        Route::put('/groups/{sqid}', [GroupController::class, 'update']);
        Route::delete('/groups/{sqid}', [GroupController::class, 'destroy']);
        Route::get('/notes', [NoteController::class, 'index']);
        Route::post('/notes', [NoteController::class, 'store']);
        Route::get('/notes/{sqid}', [NoteController::class, 'show']);
        Route::put('/notes/{sqid}', [NoteController::class, 'update']);
        Route::delete('/notes/{sqid}', [NoteController::class, 'destroy']);
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::get('/teams', [TeamController::class, 'index']);
        Route::post('/teams', [TeamController::class, 'store']);
        Route::get('/teams/{sqid}', [TeamController::class, 'show']);
        Route::put('/teams/{sqid}', [TeamController::class, 'update']);
        Route::delete('/teams/{sqid}', [TeamController::class, 'destroy']);
        Route::post('/teams/{sqid}/users', [TeamController::class, 'addUser']);
        Route::get('/teams/{sqid}/users', [TeamController::class, 'getUsers']);
        Route::put('/teams/{sqid}/users/{userId}', [TeamController::class, 'updateUser']);
        Route::delete('/teams/{sqid}/users/{userId}', [TeamController::class, 'removeUser']);
    });
});
