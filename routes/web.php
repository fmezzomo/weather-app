<?php

use App\Http\Controllers\WeatherController;
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
} );

Route::get( '/weather/{city}', [ WeatherController::class, 'getWeather' ] );