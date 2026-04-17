<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Group route untuk Autentikasi ParkirPro
Route::prefix('auth')->group(function () {
    Route::post('/check-status', [AuthController::class, 'checkStatus']);
    Route::post('/authenticate', [AuthController::class, 'authenticate']);
});
