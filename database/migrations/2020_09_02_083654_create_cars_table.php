<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('state_number')->comment('Гос номер');
            $table->foreignId('car_type_id')->constrained('car_types')->onDelete('cascade');
            $table->string('image')->nullable()->comment('Фото');
            $table->boolean('tv')->default('1')->comment('Телевизор');
            $table->boolean('conditioner')->default('1')->comment('Кондиционер');
            $table->boolean('baggage')->default('1')->comment('Багаж');
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
        Schema::dropIfExists('cars');
    }
}
