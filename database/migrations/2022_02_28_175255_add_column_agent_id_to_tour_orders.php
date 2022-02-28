<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAgentIdToTourOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id')->after('passenger_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tour_orders', function (Blueprint $table) {
            $table->dropColumn('agent_id');
        });
    }
}
