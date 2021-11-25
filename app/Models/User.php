<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = ['created_at','updated_at'];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function notConfirmedCars()
    {
        return $this->hasMany(Car::class)->where(['is_confirmed' => 0]);
    }
}
