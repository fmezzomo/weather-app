<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForecastFavoriteTable extends Migration
{
    public function up()
    {
        Schema::create('forecast_favorite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('favorite_id');
            $table->date('end_date');
            $table->json('forecast_data');
            $table->timestamps();

            $table->foreign('favorite_id')->references('id')->on('favorite_cities')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forecast_favorite');
    }
}
