<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;

class FavoriteCity extends Model
{
    use HasFactory;

    protected $table = 'favorite_cities';

    protected $fillable = [
        'user_id',
        'city_id',
        'city_name',
        'country',
        'lat',
        'lon',
    ];

    public static function addFavorite( $userId, $city )
    {
        try {
            $existingCity = self::where( 'user_id', $userId )
                                ->where( 'city_id', $city[ 'id' ] )
                                ->first();
    
            if ( $existingCity ) {
                return response()->json( [
                    'success' => false,
                    'message' => 'You already have this city as favorite.'
                ], 400 );
            }
    
            $favorite = self::create([
                'user_id'   => $userId,
                'city_id'   => $city['id'],
                'city_name' => $city['name'],
                'country'   => $city['sys']['country'],
                'lat'       => $city['coord']['lat'],
                'lon'       => $city['coord']['lon'],
            ]);

            if ( $favorite ) {
                return response()->json( [
                    'success'  => true,
                    'message'  => 'City added successfully!',
                    'favorite' => $favorite
                ], 201 );
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Fail to add the favorite city'
                ], 500);
            }
    
            
    
        } catch ( QueryException $e ) {
            return response()->json([
                'success' => false,
                'message' => 'Fail to add the favorite city: ' . $e->getMessage()
            ], 500);
        }
    }

    public static function removeFavorite( $userId, $cityId )
    {
        $favorite = self::where( 'user_id', $userId )
                        ->where( 'city_id', $cityId )
                        ->first();

        if ( ! $favorite ) {
            return response()->json( [
                'success' => false,
                'message' => 'City not found in favorites.'
            ], 400 );
        }

        try {
            $favorite->delete();

            return response()->json( [
                'success' => true,
                'message' => 'City removed from favorites.'
            ], 200 );
        } catch ( QueryException $e ) {
            return response()->json([
                'success' => false,
                'message' => 'Fail to remove the favorite city: ' . $e->getMessage()
            ], 500);
        }
    }

    // Organizar codigo
    public function forecastCity(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'user_favorite_cities', 'user_id', 'city_id')
                    ->withTimestamps();
    }
}
