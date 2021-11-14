<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Models\Car;
use App\Models\CarTravel;
use App\Models\CarTravelPlace;
use App\Models\City;
use App\Models\Station;
use App\Models\Travel;
use App\Models\TravelStation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TravelStationController extends Controller

{

    public function index($id)
    {

        $data['travel'] = Travel::join('cities as from','from.id','travel.from_city_id')
            ->join('cities as to','to.id','travel.to_city_id')
            ->select('travel.id','from.name as from','to.name as to')
            ->where('travel.id',$id)
            ->first();


        $data['travel_stations'] = TravelStation::join('travel','travel_stations.travel_id','travel.id')
            ->join('stations','stations.id','travel_stations.station_id')
            ->join('cities','cities.id','stations.city_id')
            ->where('travel_id',$id)
            ->select('travel_stations.id',
                DB::raw('CONCAT(cities.name , " - " ,stations.name ) as name'),
            )
            ->orderBy('travel_stations.position','desc')->get();
        return view('admin.travel_station.index', $data);
    }



    public function edit($id, Request $request)
    {
        $data['travel_station'] = TravelStation::findOrFail($id);
        $data['stations'] = Station::join('cities','cities.id','stations.city_id')
            ->select('stations.id',
                DB::raw('CONCAT(cities.name , " - " ,stations.name ) as name'),
            )
            ->orderBy('stations.name','desc')->get();
        return view('admin.travel_station.edit',$data);

    }
    public function add($id)
    {
        $data['travel_id'] = $id;
        $data['stations'] = Station::join('cities','cities.id','stations.city_id')
            ->select('stations.id',
                DB::raw('CONCAT(cities.name , " - " ,stations.name ) as name'),
            )
            ->orderBy('stations.name','desc')->get();

        return view('admin.travel_station.add',$data);

    }
    public function create(Request $request)
    {
        $cat = new TravelStation();
        $cat->travel_id = $request['travel_id'];
        $cat->station_id = $request['station_id'];
        $cat->save();

        return redirect()->route('admin.travel_station.index',$request['travel_id']);

    }

    public function update($id,Request $request)
    {

        $s = TravelStation::findOrFail($id);

        $s->station_id = $request['station_id'];
        $s->save();
        return redirect()->route('admin.travel_station.index',$s->travel_id);
    }

    public function destroy($id)
    {
        $l = TravelStation::findOrFail($id);
        $l->delete();
        return redirect()->back();
    }


    public function top($id)
    {
        $l = TravelStation::findOrFail($id);
        $l->position = $l->position + 1;
        $l->save();
        return redirect()->back();
    }


    public function bottom($id)
    {
        $l = TravelStation::findOrFail($id);
        if ($l->position > 0){
            $l->position = $l->position - 1;
            $l->save();
        }
        return redirect()->back();
    }


}
