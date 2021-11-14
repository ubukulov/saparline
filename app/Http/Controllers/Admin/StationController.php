<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Models\Car;
use App\Models\CarTravel;
use App\Models\CarTravelPlace;
use App\Models\City;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StationController extends Controller

{

    public function index($id)
    {

        $data['city'] = City::findOrFail($id);
        $data['stations'] = Station::where('city_id',$id)->get();
        return view('admin.station.index', $data);
    }



    public function edit($id, Request $request)
    {
        $data['station'] = Station::findOrFail($id);

        return view('admin.station.edit',$data);

    }
    public function add($id)
    {
        $data['city_id'] = $id;
        return view('admin.station.add',$data);

    }
    public function create(Request $request)
    {
        $cat = new Station();
        $cat->name = $request['name'];
        $cat->city_id = $request['city_id'];
        $cat->lat = $request['lat'];
        $cat->lng = $request['lng'];
        $cat->save();

        return redirect()->route('admin.station.index',$request['city_id']);

    }

    public function update($id,Request $request)
    {

        $s = Station::findOrFail($id);

        if ($request['name']){
            $s->name = $request['name'];
        }
        if ($request['lat']){
            $s->lat = $request['lat'];
        }
        if ($request['lng']){
            $s->lng = $request['lng'];
        }

        $s->save();
        return redirect()->route('admin.station.index',$s->city_id);
    }

    public function destroy($id)
    {
        $l = Station::findOrFail($id);
        $l->delete();
        return redirect()->back();
    }


}
