<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCarTravelPlaceOrders2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_travel_place_orders', function (Blueprint $table) {
            $table->text('reason_for_return')->after('iin')->nullable();
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
            $table->dropColumn('reason_for_return');
        });
    }
}
