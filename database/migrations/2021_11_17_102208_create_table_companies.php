<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('bin')->nullable();
			$table->unsignedTinyInteger('type_id')->default(1);
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
        Schema::dropIfExists('companies');
    }
}
