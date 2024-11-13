<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForecastFavorite extends Model
{
    use HasFactory;

    protected $table = 'forecast_favorite';

    protected $fillable = [
        'id', 'favorite_id', 'end_date', 'forecast_data',
    ];
}
