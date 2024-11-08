<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('favorite_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('city_id');
            $table->string('city_name', 100);
            $table->string('country', 5);
            $table->float('lat');
            $table->float('lon');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorite_cities');
    }
}
