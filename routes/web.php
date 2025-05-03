<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

Route::get('/', [MovieController::class, 'discover']);
Route::get('/discover', [MovieController::class, 'discover']);
Route::get('/load-more', [MovieController::class, 'loadMore']);
Route::get('/movies/{id}', [MovieController::class, 'show'])->where('id', '[0-9]+');
