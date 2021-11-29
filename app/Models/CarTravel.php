<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CarTravelPlace;

class CarTravel extends Model
{
    protected $hidden = ['created_at','updated_at'];

	public function get_all_places()
	{
		return $this->hasMany(CarTravelPlace::class, 'car_travel_id');
	}
}
