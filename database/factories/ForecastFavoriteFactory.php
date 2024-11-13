<?php

namespace Database\Factories;

use App\Models\ForecastFavorite;
use App\Models\FavoriteCity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForecastFavoriteFactory extends Factory
{
    protected $model = ForecastFavorite::class;

    public function definition()
    {
        return [
            'favorite_id'   => FavoriteCity::factory(),
            'forecast_data' => json_encode( [ 'data' => 'forecast data' ] ),
            'end_date'      => now()->addDays( 3 )->format( 'Y-m-d' ),
        ];
    }
}
