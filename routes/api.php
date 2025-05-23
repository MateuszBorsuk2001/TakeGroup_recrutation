<?php

use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\SerieController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('movies')->group(function () {
    Route::get('/', [MovieController::class, 'index']);
    Route::get('/{id}', [MovieController::class, 'show']);
});

Route::prefix('series')->group(function () {
    Route::get('/', [SerieController::class, 'index']);
    Route::get('/{id}', [SerieController::class, 'show']);
});

Route::prefix('genres')->group(function () {
    Route::get('/', [GenreController::class, 'index']);
    Route::get('/{id}', [GenreController::class, 'show']);
    Route::get('/{id}/movies', [GenreController::class, 'movies']);
    Route::get('/{id}/series', [GenreController::class, 'series']);
});

