<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    use HasFactory;

    protected $table = 'weather_data';

    protected $fillable = [
        'city_id', 'city_name', 'country', 'lat', 'lon', 'temp_min', 'temp_max', 'condition', 'condition_icon', 'condition_description'
    ];
}
