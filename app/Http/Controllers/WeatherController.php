<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function findCity($city)
    {
        $apiKey = env('VITE_API_KEY');
        $apiURL = env('VITE_API_URL');
        $url    = $apiURL . "/find?q={$city}&appid={$apiKey}&units=metric";

        $response = Http::get($url);

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
        $url = $apiURL . "/forecast?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

        $response = Http::get($url);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Unable to fetch weather data'], 500);
    }
}
