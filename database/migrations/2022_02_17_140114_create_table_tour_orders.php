<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTourOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('passenger_id')->nullable();
            $table->integer('number');
            $table->enum('status', [
                'free', 'in_process', 'take', 'cancel'
            ])->default('free');
            $table->integer('price')->nullable();
            $table->string('first_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('iin')->nullable();
            $table->text('reason_for_return')->nullable();
            $table->timestamp('booking_time')->nullable();

            $table->foreign('tour_id')
                ->references('id')
                ->on('tours')
                ->onDelete('cascade');

            $table->foreign('passenger_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('tour_orders');
    }
}
