<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarTravelPlaceOrder extends Model
{
    protected $hidden = ['updated_at'];

    protected $casts= [
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];
}
