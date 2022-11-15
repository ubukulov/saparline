<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDirectionPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direction_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_id');
            $table->unsignedBigInteger('car_type_id');
            $table->integer('number');
            $table->integer('price');
            $table->timestamps();

            $table->foreign('travel_id')
                ->references('id')
                ->on('travel')
                ->onDelete('cascade');

            $table->foreign('car_type_id')
                ->references('id')
                ->on('car_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direction_prices');
    }
}
