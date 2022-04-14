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

    public function getOrderStats()
    {
        $orders = $this->orders;
        $arr['countSoldPlaces'] = 0;
        $arr['countFreePlaces'] = 0;
        foreach($orders as $order){
            if($order->status == 'take') $arr['countSoldPlaces'] += 1;
            if($order->status == 'free') $arr['countFreePlaces'] += 1;
        }
        return $arr;
    }

    public function getCountFreePlaces()
    {
        //$orders = $this->orders;
        $orders = TourOrder::where('tour_id', $this->id)->get();
        $countFreePlaces = 0;
        foreach($orders as $order){
            if($order->status == 'free') $countFreePlaces += 1;
        }
        return $countFreePlaces;
    }

    public function getFreePlaceForBooking($count)
    {
        $orders = $this->orders;
        $free_places = collect();
        foreach($orders as $order) {
            if(count($free_places) == $count) break;
            if($order->status == 'free') $free_places->push($order);
        }
        return $free_places;
    }
}
