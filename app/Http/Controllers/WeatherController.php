<?php

namespace App\Http\Controllers;

use App\Models\WeatherData;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function findCity( $city )
    {
        $apiKey = env('VITE_API_KEY');
        $apiURL = env('VITE_API_URL');

        $response = Http::get( $apiURL . '/find', [
            'q'     => $city,
            'units' => 'metric',
            'appid' => $apiKey,//config('services.weather.api_key')
        ]);

        if ( $response->successful() ) {

            $data = $response->json();

            if ( array_key_exists( 'list', $data ) && count( $data[ 'list' ] ) > 0 ) {
                foreach ( $data[ 'list' ] as $cityData ) {
                    $cityRecord = WeatherData::create(
                        [
                            'city_id'               => $cityData['id'],
                            'city_name'             => $cityData['name'],
                            'country'               => $cityData['sys']['country'],
                            'lat'                   => $cityData['coord']['lat'],
                            'lon'                   => $cityData['coord']['lon'],
                            'temp_min'              => $cityData['main']['temp_min'],
                            'temp_max'              => $cityData['main']['temp_max'],
                            'condition'             => $cityData['weather'][0]['main'],
                            'condition_icon'        => $cityData['weather'][0]['icon'],
                            'condition_description' => $cityData['weather'][0]['description'],
                        ]
                    );
                }
            }
            return response()->json( $data );
        }

        return response()->json( [ 'error' => 'City not found' ], 404 );
    }

    public function getWeather( Request $request )
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

        if ( $response->successful() ) {
            return response()->json ($response->json() );
        }

        return response()->json ([ 'error' => 'Unable to fetch weather data' ], 500 );
    }
}



