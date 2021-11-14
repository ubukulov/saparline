<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role',['passenger','driver']);
            $table->string('phone')->unique();
            $table->string('name')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('token')->unique();
            $table->string('device_token')->nullable();

            $table->string('passport_image')->nullable();
            $table->string('identity_image')->nullable();

            $table->boolean('push')->default(1);
            $table->boolean('sound')->default(1);

            $table->enum('confirmation',['waiting','confirm','reject'])->nullable();

            $table->enum('lang',['ru','kz'])->default('ru');


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
        Schema::dropIfExists('users');
    }
}
