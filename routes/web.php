<?php

use App\Http\Controllers\FavoriteCityController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get ('/', function () {
    return Inertia::render( 'Welcome', [
        'canLogin' => Route::has( 'login' )
    ]);
});

Route::middleware( [
    'auth:sanctum',
    config( 'jetstream.auth_session' ),
    'verified',
] )->group( function () {
    Route::get( '/dashboard', function () {
        return Inertia::render( 'Dashboard' );
    } )->name( 'dashboard' );

    Route::get('/find/{city}', [ WeatherController::class, 'findCity' ]);
    Route::get('/weather/{lat}/{lon}', [ WeatherController::class, 'getWeather' ]);
    
    Route::get('/favorites', [FavoriteCityController::class, 'index']);
    Route::post('/favorites', [FavoriteCityController::class, 'store']);
    Route::delete('/favorites/{cityId}', [FavoriteCityController::class, 'destroy']);
} );