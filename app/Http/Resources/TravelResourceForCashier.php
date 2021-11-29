<?php

namespace App\Http\Resources;

use App\Models\Car;
use App\Models\CarTravelPlace;
use App\Models\CarTravelStation;
use App\Models\CommentDislike;
use App\Models\CommentLike;
use App\Models\Favorite;
use App\Models\Post;
use App\Models\Station;
use App\Models\Subscription;
use App\Models\Travel;
use App\Models\CompanyCar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TravelResourceForCashier extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'departure_time' => Carbon::parse($this->departure_time)->format('H:i'),
            'destination_time' => $this->destination_time,
            'car' => Car::join('users','users.id','cars.user_id')
                ->join('car_types','car_types.id','cars.car_type_id')
                ->select('cars.*','users.name','users.phone','car_types.name as car_type','car_types.count_places as car_type_count_places')
                ->find($this->car_id),
            'from' => Station::join('cities','cities.id','stations.city_id')
                ->where('stations.id',$this->from_station_id)
                ->select('cities.id as city_id','cities.name as city','stations.id as station_id','stations.name as station','lat','lng')
                ->first(),
            'to' => Station::join('cities','cities.id','stations.city_id')
                ->where('stations.id',$this->to_station_id)
                ->select('cities.id as city_id','cities.name as city','stations.id as station_id','stations.name as station','lat','lng')
                ->first(),
            'min_price' => CarTravelPlace::where('car_travel_id',$this->id)->min('price'),
            'max_price' => CarTravelPlace::where('car_travel_id',$this->id)->max('price'),
            'count_free_places' => CarTravelPlace::where('car_travel_id',$this->id)->where('status','free')->count(),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H-i-s'),
			'company_car' => CompanyCar::where(['car_id' => $this->car_id])
								->with('company')
								->first(),
            'stations' => CarTravelStation::join('stations','stations.id','car_travel_stations.station_id')
                ->join('cities','cities.id','stations.city_id')
                ->where('car_travel_id',$this->id)
                ->select('cities.name as city',
                    DB::raw('CONCAT(cities.name , " - " ,stations.name ) as stations'),
                    'stations.id as station_id' ,
                    'lat','lng'
                )
                ->get()

        ];

    }
}
