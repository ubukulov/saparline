<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCarTravelPlaceOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_travel_place_orders', function (Blueprint $table) {
            $table->string('first_name')->after('added')->nullable();
            $table->string('phone')->after('first_name')->nullable();
            $table->string('iin')->after('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_travel_place_orders', function (Blueprint $table) {
            $table->dropColumns(['first_name', 'phone', 'iin']);
        });
    }
}
