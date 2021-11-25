<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Models\Car;
use App\Models\CarTravel;
use App\Models\CarTravelPlace;
use App\Models\CarType;
use App\Models\City;
use App\Models\Travel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TravelController extends Controller

{

    public function index()
    {

        $data['travel'] = Travel::join('cities as from','from.id','travel.from_city_id')
            ->join('cities as to','to.id','travel.to_city_id')
            ->select('travel.id','from.name as from','to.name as to')
            ->get();
        return view('admin.travel.index', $data);
    }



    public function edit($id, Request $request)
    {
        $data['travel'] = Travel::findOrFail($id);
        $data['cities'] = City::all();
        return view('admin.travel.edit',$data);

    }
    public function add()
    {
        $data['cities'] = City::all();
        return view('admin.travel.add',$data);

    }
    public function create(Request $request)
    {
        $cat = new travel();
        $cat->from_city_id = $request['from_city_id'];
        $cat->to_city_id = $request['to_city_id'];
        $cat->save();

        return redirect()->route('admin.travel.index');

    }

    public function update($id,Request $request)
    {

        $s = Travel::findOrFail($id);

        $s->from_city_id = $request['from_city_id'];
        $s->to_city_id = $request['to_city_id'];
        $s->save();
        return redirect()->route('admin.travel.index',$s->city_id);
    }

    public function destroy($id)
    {
        $l = Travel::findOrFail($id);
        $l->delete();
        return redirect()->back();
    }

    public function carTypes(){
        return view('admin.carTypesIndex');
    }

    public function addCarType(Request $request){
        $rules = [
            'name'=> 'required',
            'count_places'=> 'required|unique:car_types,count_places',
        ];
        $messages = [

        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }
        $carType = new CarType();
        $carType->name = $request['name'];
        $carType->count_places = $request['count_places'];
        $carType->save();

        return back()->withMessage('success');
    }

    public function carTypeDestroy(Request $request){
           return back();
    }


}
