<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CarTravelPlace;
use App\Models\Car;

class CarTravel extends Model
{
    protected $hidden = ['created_at','updated_at'];

	public function get_all_places()
	{
		return $this->hasMany(CarTravelPlace::class, 'car_travel_id');
	}

	public function car()
	{
		return $this->belongsTo(Car::class);
	}

	public function get_all_places_orders()
    {
        return $this->hasMany(CarTravelPlaceOrder::class, 'car_travel_id');
    }
}
