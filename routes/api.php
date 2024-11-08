<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/find/{city}', [ WeatherController::class, 'findCity' ] );
Route::get( '/weather', [ WeatherController::class, 'getWeather' ] );