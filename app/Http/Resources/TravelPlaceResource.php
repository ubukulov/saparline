<?php

namespace App\Http\Resources;

use App\Models\Car;
//use App\Models\CommentDislike;
//use App\Models\CommentLike;
//use App\Models\Favorite;
//use App\Models\Post;
use App\Models\Station;
//use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelPlaceResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'driver' => User::where('id',$this->driver_id)->select('id','name','phone','avatar')->first(),
            'passenger' => User::where('id',$this->passenger_id)->select('id','name','phone','avatar')->first(),
            'car' => Car::join('car_travel','car_travel.car_id','cars.id')
                ->where('car_travel.id',$this->car_travel_id)
                ->select('cars.*')
                ->first(),
            'from' => Station::join('cities','cities.id','stations.city_id')
                ->where('stations.id',$this->from_station_id)
                ->select('stations.id','stations.name as station','cities.name as city','lat','lng')
                ->first(),
            'to' => Station::join('cities','cities.id','stations.city_id')
                ->where('stations.id',$this->to_station_id)
                ->select('stations.id','stations.name as station','cities.name as city','lat','lng')
                ->first(),
            'price' => $this->price,
            'booking_time' => $this->booking_time,
            'status' => $this->status,
            'number'=> $this->number,
            'first_name'=> $this->first_name,
            'phone'=> $this->phone,
            'iin'=> $this->iin,

        ];

    }
}
