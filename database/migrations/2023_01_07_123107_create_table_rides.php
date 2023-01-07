<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRides extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('from_city_id');
            $table->unsignedBigInteger('to_city_id');
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->integer('price');
            $table->string('comments', 255)->nullable();
            $table->date('departure_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->enum('status', ['not', 'ok']);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('from_city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade');

            $table->foreign('to_city_id')
                ->references('id')
                ->on('cities')
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
        Schema::dropIfExists('rides');
    }
}
