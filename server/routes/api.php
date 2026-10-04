<?php

use App\Enums\MediaType;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    Route::get('/search', [SearchController::class, 'search'])
        ->name('search');

    /*
    |--------------------------------------------------------------------------
    | Media (scoped by type: movie | tv)
    |--------------------------------------------------------------------------
    */

    Route::prefix('{type}')
        ->whereIn('type', MediaType::cases())
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Home
            |--------------------------------------------------------------------------
            */
            Route::get('/home', [HomeController::class, 'index'])
                ->name('home');

            /*
            |--------------------------------------------------------------------------
            | Genres
            |--------------------------------------------------------------------------
            */
            Route::get('/genres', [GenreController::class, 'index'])
                ->name('genres.index');

            Route::get('/genres/{genreId}', [GenreController::class, 'media'])
                ->whereNumber('genreId')
                ->name('genres.media');

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            Route::get('/{tmdbId}', [MovieController::class, 'show'])
                ->whereNumber('tmdbId')
                ->name('media.show');

            Route::get('/{tmdbId}/trailer', [MovieController::class, 'trailer'])
                ->whereNumber('tmdbId')
                ->name('media.trailer');
        });
});

/*
|--------------------------------------------------------------------------
| Authenticated API
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
