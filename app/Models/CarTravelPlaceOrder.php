<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CarTravel;
use App\Models\User;
use App\Models\Station;

class CarTravelPlaceOrder extends Model
{
    protected $hidden = ['updated_at'];

    protected $casts= [
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];
	
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
