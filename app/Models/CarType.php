<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarType extends Model
{
    protected $hidden = ['created_at','updated_at'];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
