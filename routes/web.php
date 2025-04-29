<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

Route::get('/', [MovieController::class, 'discover']);
Route::get('/movies', [MovieController::class, 'search']);
Route::get('/discover', [MovieController::class, 'discover']);
