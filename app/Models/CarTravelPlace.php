<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarTravelPlace extends Model
{
    protected $hidden = ['created_at','updated_at'];
	
	public function driver()
	{
		return $this->belongsTo(User::class);
	}
	
	public function from_station()
	{
		return $this->belongsTo(Station::class, 'from_station_id');
	}
	
	public function to_station()
	{
		return $this->belongsTo(Station::class, 'to_station_id');
	}
}
