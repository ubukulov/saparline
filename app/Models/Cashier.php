<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Cashier extends Authenticatable
{
	use Notifiable;
	
    protected $fillable = [
        'email', 'password', 'city_id', 'first_name', 'last_name', 'active', 'station_id', 'company_name', 'company_id',
		'phone', 'type_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
