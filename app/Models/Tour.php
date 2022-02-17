<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'city_id', 'resting_place_id', 'meeting_place_id', 'car_id', 'title', 'departure_time', 'destination_time',
        'description', 'tour_price', 'seat_price'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function resting_place()
    {
        return $this->belongsTo(RestPlace::class);
    }

    public function meeting_place()
    {
        return $this->belongsTo(MeetPlace::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function images()
    {
        return $this->hasMany(TourImage::class);
    }

    public function orders()
    {
        return $this->hasMany(TourOrder::class);
    }
}
