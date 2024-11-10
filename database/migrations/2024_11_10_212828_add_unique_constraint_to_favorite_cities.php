<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToFavoriteCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favorite_cities', function (Blueprint $table) {
            $table->unique(['user_id', 'city_id']);
        });
    }

    /**
     * Revert the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorite_cities', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'city_id']);
        });
    }
}
