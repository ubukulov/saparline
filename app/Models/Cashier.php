<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    protected $fillable = [
        'username', 'password', 'city_id', 'first_name', 'last_name', 'active'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
