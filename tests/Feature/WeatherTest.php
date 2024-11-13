<?php

namespace Tests\Feature;

use App\Models\FavoriteCity;
use App\Models\ForecastFavorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the valid response when a city exists.
     *
     * @return void
     */
    public function testValidCityInput()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs( $user );

        $city     = 'London';
        $response = $this->get( "/find/{$city}" );

        $response->assertStatus( 200 );
        $response->assertJson( [
            'message' => 'accurate',
            'cod'     => 200,
            'count'   => 5,
        ] );
        $response->assertJsonStructure( [
            'message',
            'cod',
            'count',
            'list' => [
                '*' => [
                    'id',
                    'name',
                    'coord'   => [ 'lat', 'lon' ],
                    'main'    => [ 'temp', 'feels_like', 'temp_min', 'temp_max', 'pressure', 'humidity', 'sea_level', 'grnd_level' ],
                    'dt',
                    'wind'    => [ 'speed', 'deg' ],
                    'sys'     => [ 'country' ],
                    'rain',
                    'snow',
                    'clouds'  => [ 'all' ],
                    'weather' => [
                        '*' => [ 'id', 'main', 'description', 'icon' ]
                    ],
                ],
            ],
        ] );
    }

    /**
     * Test the error response when a city is not found.
     *
     * @return void
     */
    public function testInvalidCityInput()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs( $user );

        $city     = 'InvalidCityName';
        $response = $this->get ( "/find/{$city}" ) ;

        $response->assertStatus (200 );

        $response->assertJson ( [
            'message' => 'accurate',
            'cod'     => 200,
            'count'   => 0,
            'list'    => [],
        ] );
    }

    /**
     * Test the favorites listing endpoint when returns empty.
     *
     * @return void
     */
    public function testFavoritesReturnEmptyWhenNoData()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs( $user );

        $response = $this->get( '/favorites' );

        $response->assertStatus( 200 );
        $response->assertJson( [] );
    }

    /**
     * Test the favorites listing endpoint.
     *
     * @return void
     */
    public function testFavoritesListing()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs( $user );

        $favorite = FavoriteCity::factory()->create( [
            'user_id'   => $user->id,
            'city_name' => 'London',
            'city_id'   => 2643743,
            'country'   => 'GB',
            'lat'       => 51.5085,
            'lon'       => -0.1257,
        ] );
        $forecast = ForecastFavorite::factory()->create( [
            'favorite_id'   => $favorite->id,
            'forecast_data' => '{"data":"forecast data"}',
        ] );

        $response = $this->actingAs( $user )->get( '/favorites' );

        $response->assertStatus( 200 );
        $response->assertJson( [
            [
                "id"         => $favorite->id,
                "user_id"    => $favorite->user_id,
                "city_id"    => $favorite->city_id,
                "city_name"  => $favorite->city_name,
                "country"    => $favorite->country,
                "lat"        => $favorite->lat,
                "lon"        => $favorite->lon,
                "created_at" => $favorite->created_at->toISOString(),
                "updated_at" => $favorite->updated_at->toISOString(),
                "forecast" => [
                    "id"            => $forecast->id,
                    "favorite_id"   => $forecast->favorite_id,
                    "forecast_data" => $forecast->forecast_data,
                ]
            ]
        ] );
    }

    /**
     * Test the adding a city to favorites.
     *
     * @return void
     */
    public function testAddCityToFavorites()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs( $user );

        $favorite1 = FavoriteCity::factory()->create( [
            'user_id'    => $user->id,
            'city_id'    => 2643743,
            'city_name'  => 'London',
            'country'    => 'GB',
            'lat'        => 51.5085,
            'lon'        => -0.1257,
            'created_at' => '2024-11-13T14:25:40.000000Z',
            'updated_at' => '2024-11-13T14:25:40.000000Z',
        ] );
    
        $favorite2 = FavoriteCity::factory()->create( [
            'user_id'    => $user->id,
            'city_id'    => 6058560,
            'city_name'  => 'London',
            'country'    => 'CA',
            'lat'        => 42.9834,
            'lon'        => -81.233,
            'created_at' => '2024-11-13T14:32:28.000000Z',
            'updated_at' => '2024-11-13T14:32:28.000000Z',
        ] );
    
        $forecast1 = ForecastFavorite::factory()->create ( [
            'favorite_id'   => $favorite1->id,
            'end_date'      => '2024-11-16',
            'forecast_data' => '',
            'created_at'    => '2024-11-13T14:25:41.000000Z',
            'updated_at'    => '2024-11-13T14:25:41.000000Z',
        ] ) ;
    
        $forecast2 = ForecastFavorite::factory()->create( [
            'favorite_id'   => $favorite2->id,
            'end_date'      => '2024-11-16',
            'forecast_data' => '',
            'created_at'    => '2024-11-13T14:32:29.000000Z',
            'updated_at'    => '2024-11-13T14:32:29.000000Z',
        ] );
    
        $response = $this->actingAs( $user )->get( '/favorites' );
    
        $response->assertStatus (200 );
        $response->assertJson( [
            [
                "id"         => $favorite1->id,
                "user_id"    => $user->id,
                "city_id"    => 2643743,
                "city_name"  => "London",
                "country"    => "GB",
                "lat"        => 51.5085,
                "lon"        => -0.1257,
                "created_at" => "2024-11-13T14:25:40.000000Z",
                "updated_at" => "2024-11-13T14:25:40.000000Z",
                "forecast"   => [
                    "id"            => $forecast1->id,
                    "favorite_id"   => $favorite1->id,
                    "end_date"      => "2024-11-16",
                    "forecast_data" => "",
                    "created_at"    => "2024-11-13T14:25:41.000000Z",
                    "updated_at"    => "2024-11-13T14:25:41.000000Z"
                ]
            ],
            [
                "id" => $favorite2->id,
                "user_id"    => $user->id,
                "city_id"    => 6058560,
                "city_name"  => "London",
                "country"    => "CA",
                "lat"        => 42.9834,
                "lon"        => -81.233,
                "created_at" => "2024-11-13T14:32:28.000000Z",
                "updated_at" => "2024-11-13T14:32:28.000000Z",
                "forecast"   => [
                    "id"            => $forecast2->id,
                    "favorite_id"   => $favorite2->id,
                    "end_date"      => "2024-11-16",
                    "forecast_data" => "",
                    "created_at"    => "2024-11-13T14:32:29.000000Z",
                    "updated_at"    => "2024-11-13T14:32:29.000000Z"
                ]
            ]
        ] );
    }

    /**
     * Test the removing a city from favorites.
     *
     * @return void
     */
    public function testRemoveCityFromFavorites()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs( $user );

        $favorite = FavoriteCity::factory()->create( [
            'user_id'   => $user->id,
            'city_id'   => 2643743,
            'city_name' => 'London',
            'country'   => 'GB',
        ] );
    
        $response = $this->actingAs( $user )->delete( "/favorites/{$favorite->city_id}" );

        $response->assertStatus( 200 );
        $response->assertJson( [
            "success" => true,
            "message" => "City removed from favorites."
        ] );
    
        $this->assertDatabaseMissing( 'favorite_cities', [
            'id'      => $favorite->id,
            'user_id' => $user->id,
        ] );
    }
}
