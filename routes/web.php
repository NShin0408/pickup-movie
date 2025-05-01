<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

Route::get('/', [MovieController::class, 'discover']);
Route::get('/discover', [MovieController::class, 'discover']);
