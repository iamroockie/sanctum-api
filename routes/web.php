<?php

use App\Support\Response;
use Illuminate\Support\Facades\Route;

Route::fallback(fn () => Response::fail(['message' => 'Ничего не найдено!'], 404));
