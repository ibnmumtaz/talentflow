<?php

use App\Http\Controllers\Api\V1\ApplicationApiController;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\CompanyApiController;
use App\Http\Controllers\Api\V1\JobApiController;
use App\Http\Controllers\Api\V1\NotificationApiController;
use App\Http\Controllers\Api\V1\SavedJobApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    Route::post('/auth/login', [AuthApiController::class, 'login']);
    Route::get('/jobs', [JobApiController::class, 'index']);
    Route::get('/jobs/{job}', [JobApiController::class, 'show']);
    Route::get('/companies', [CompanyApiController::class, 'index']);
    Route::get('/companies/{company}', [CompanyApiController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthApiController::class, 'me']);
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::get('/applications', [ApplicationApiController::class, 'index']);
        Route::get('/applications/{application}', [ApplicationApiController::class, 'show']);
        Route::post('/applications/{application}/transition', [ApplicationApiController::class, 'transition']);
        Route::get('/saved-jobs', [SavedJobApiController::class, 'index']);
        Route::post('/saved-jobs/{job}/toggle', [SavedJobApiController::class, 'toggle']);
        Route::get('/notifications', [NotificationApiController::class, 'index']);
        Route::post('/notifications/read-all', [NotificationApiController::class, 'readAll']);
    });
});
