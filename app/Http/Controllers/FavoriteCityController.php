<?php

namespace App\Http\Controllers;

use App\Models\FavoriteCity;
use App\Models\ForecastFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteCityController extends Controller
{
    protected $daysToAdd = 3;

    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch cities and their forecasts where the end_date is greater than or equal to the current time
        $favoriteCities = FavoriteCity::where( 'user_id', $user->id )
            ->with( [ 'forecast' => function ( $query ) {
                $query->where( 'end_date', '>=', now() );
            } ] )
            ->get();

        // If there's no forecast or it expired, fetch fresh data
        foreach ( $favoriteCities as $favoriteCity ) {
            $forecast = isset( $favoriteCity->forecast ) ? $favoriteCity->forecast->first() : null;
            if ( ! $forecast || $forecast->end_date < now() ) {
                $this->storeForecastData( $favoriteCity );

                $forecast = ForecastFavorite::where('favorite_id', $favoriteCity->id)
                    ->where('end_date', '>=', now())
                    ->first();
            }
            $favoriteCity->forecast = $forecast;
        }

        return response()->json( $favoriteCities );
    }

    public function store( Request $request )
    {
        $request->validate([
            'city'             => 'required|array',
            'city.id'          => 'required|integer',
            'city.name'        => 'required|string',
            'city.sys.country' => 'required|string',
            'city.coord.lat'   => 'required|numeric',
            'city.coord.lon'   => 'required|numeric',
        ]);

        $cityData = $request->input( 'city' );
        $user     = Auth::user();
        
        $FavoriteCity     = new FavoriteCity();
        $favoriteResponse = $FavoriteCity->addFavorite( $user->id, $cityData );

        $data = $favoriteResponse->getData();

        if ( isset( $data->success ) && $data->success === true ) {
            $this->storeForecastData( $data->favorite );

            return $favoriteResponse;
        }

        return false;
    }

    public function destroy( $cityId )
    {
        $user = Auth::user();

        $FavoriteCity = new FavoriteCity();
        return $FavoriteCity->removeFavorite( $user->id, $cityId );
    }

    private function storeForecastData( $favorite )
    {
        $weatherController = new WeatherController();
        $forecastData      = $weatherController->getWeather( $favorite->lat, $favorite->lon );

        $endDate = Carbon::now()->addDays($this->daysToAdd)->format('Y-m-d');

        $forecastFavorite = new ForecastFavorite([
            'favorite_id'   => $favorite->id,
            'end_date'      => $endDate,
            'forecast_data' => json_encode($forecastData),
        ]);
        $forecastFavorite->save();
    }
}
