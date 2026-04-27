<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Http\Request;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('translations', TranslationController::class);
    Route::get('export', [TranslationController::class, 'export']);
});

