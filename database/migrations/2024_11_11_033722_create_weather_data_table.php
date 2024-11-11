<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeatherDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id');
            $table->string('city_name');
            $table->string('country');
            $table->decimal('lat', 10, 6);
            $table->decimal('lon', 10, 6);
            $table->decimal('temp_min', 5, 2);
            $table->decimal('temp_max', 5, 2);
            $table->string('condition');
            $table->string('condition_icon');
            $table->text('condition_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weather_data');
    }
}
