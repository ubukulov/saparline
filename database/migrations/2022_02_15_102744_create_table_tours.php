<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTours extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('resting_place_id');
            $table->unsignedBigInteger('meeting_place_id');
            $table->unsignedBigInteger('car_id');
            $table->string('title');
            $table->timestamp('departure_time')->nullable();
            $table->timestamp('destination_time')->nullable();
            $table->text('description')->nullable();
            $table->integer('tour_price');
            $table->integer('seat_price');

            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade');

            $table->foreign('resting_place_id')
                ->references('id')
                ->on('resting_places')
                ->onDelete('cascade');

            $table->foreign('meeting_place_id')
                ->references('id')
                ->on('meeting_place')
                ->onDelete('cascade');

            $table->foreign('car_id')
                ->references('id')
                ->on('cars')
                ->onDelete('cascade');

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
        Schema::dropIfExists('tours');
    }
}
