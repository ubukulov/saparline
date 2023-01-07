<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    protected $fillable = [
        'user_id', 'from_city_id', 'to_city_id', 'from_address', 'to_address', 'price', 'comments', 'departure_date', 'departure_time',
        'status'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function from_city()
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function to_city()
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }
}
