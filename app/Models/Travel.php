<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    protected $hidden = ['created_at','updated_at'];

    public function from_city()
    {
        return $this->belongsTo(City::class, 'id', 'from_city_id');
    }

    public function to_city()
    {
        return $this->belongsTo(City::class, 'id', 'to_city_id');
    }
}
