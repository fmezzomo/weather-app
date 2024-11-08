<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function findCity($city)
    {
        $apiKey = env('VITE_API_KEY');
        $apiURL = env('VITE_API_URL');

        $response = Http::get( $apiURL . '/find', [
            'q'     => $city,
            'units' => 'metric',
            'appid' => $apiKey,//config('services.weather.api_key')
        ]);

        if ($response->ok()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'City not found'], 404);
    }

    public function getWeather(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $apiKey = env('VITE_API_KEY');
        $apiURL = env('VITE_API_URL');

        $response = Http::get( $apiURL . '/forecast', [
            'lat'     => $lat,
            'lon'     => $lon,
            'units' => 'metric',
            'appid' => $apiKey,//config('services.weather.api_key')
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Unable to fetch weather data'], 500);
    }
}



