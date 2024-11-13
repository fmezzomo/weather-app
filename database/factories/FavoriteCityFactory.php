<?php

namespace Database\Factories;

use App\Models\FavoriteCity;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteCityFactory extends Factory
{
    protected $model = FavoriteCity::class;

    public function definition()
    {
        return [
            'user_id'   => \App\Models\User::factory(),
            'city_name' => $this->faker->city,
            'city_id'   => $this->faker->randomNumber (7 ),
            'country'   => $this->faker->countryCode,
            'lat'       => $this->faker->latitude,
            'lon'       => $this->faker->longitude,
        ];
    }
}
