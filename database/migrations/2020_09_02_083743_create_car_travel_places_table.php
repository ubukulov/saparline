<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarTravelPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_travel_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_travel_id')->constrained('car_travel')->onDelete('cascade');
            $table->foreignId('driver_id')->comment('Водитель')->constrained('users')->onDelete('cascade'); //passenger
            $table->foreignId('passenger_id')->comment('Пассажир')->constrained('users')->onDelete('cascade'); //passenger
            $table->foreignId('from_station_id')->constrained('stations')->onDelete('cascade');
            $table->foreignId('to_station_id')->constrained('stations')->onDelete('cascade');
            $table->enum('status',['free','in_process','take'])->comment('Статус')->default('free');
            $table->integer('number')->comment('Номер места');
            $table->integer('price');
            $table->enum('added',['admin','driver']);
            $table->timestamp('booking_time')->nullable();
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
        Schema::dropIfExists('car_travel_places');
    }
}
