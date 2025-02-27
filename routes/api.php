<?php

use App\Http\Controllers\Auth\AuthController;
use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => Response::success(['welcome' => 'Добро пожаловать!']))
    ->name('welcome');

Route::get('/sanctum/csrf-cookie', fn () => Response::success(['csrf-token' => csrf_token()]))
    ->name('csrf-cookie');

Route::prefix('auth/web')->group(function () {
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('guest')
        ->name('login');
    Route::post('register', [AuthController::class, 'register'])
        ->middleware('guest')
        ->name('register');

    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('logout');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
