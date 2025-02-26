<?php

use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => Response::success(['welcome' => 'Добро пожаловать!']))
    ->name('welcome');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
