<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Station;

class City extends Model
{
    protected $hidden = ['created_at','updated_at'];
	
	public function stations()
	{
		return $this->hasMany(Station::class);
	}
}
