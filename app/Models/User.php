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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function lodger_cars()
    {
        return $this->belongsToMany(Car::class, 'lodger_cars');
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }
}
