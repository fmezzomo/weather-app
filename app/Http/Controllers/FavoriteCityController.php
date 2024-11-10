<?php

namespace App\Http\Controllers;

use App\Models\FavoriteCity;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteCityController extends Controller
{
    public function index( Request $request )
    {
        $user           = User::find( 1 );//Auth::user();
        $favoriteCities = FavoriteCity::where( 'user_id', $user->id )->get();

        return response()->json($favoriteCities);
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
        $user     = User::find( 1 );//Auth::user();
        
        $FavoriteCity = new FavoriteCity();
        return $FavoriteCity->addFavorite( $user->id, $cityData );
    }

    public function destroy( $cityId )
    {
        $user = User::find( 1 );//Auth::user();

        $FavoriteCity = new FavoriteCity();
        return $FavoriteCity->removeFavorite( $user->id, $cityId );
    }
}
