<?php

use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => Response::success(['welcome' => 'Добро пожаловать!']))
    ->name('welcome');

Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    ]);

    return Response::success(['name' => $request->name]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
