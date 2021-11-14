<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Models\Car;
use App\Models\CarTravel;
use App\Models\CarTravelPlace;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CityController extends Controller

{

    public function index(Request $request)
    {


        $data['cities'] = City::all();
        return view('admin.city.index', $data);
    }


    public function edit($id, Request $request)
    {
        $data['city'] = City::findOrFail($id);

        return view('admin.city.edit',$data);

    }
    public function add()
    {
        return view('admin.city.add');

    }
    public function create(Request $request)
    {
        $cat = new City();
        $cat->name = $request['name'];
        $cat->save();

        return redirect()->route('admin.city.index');

    }

    public function update($id,Request $request)
    {

        $cat = City::findOrFail($id);

        if ($request['name']){
            $cat->name = $request['name'];
        }

        $cat->save();
        return redirect()->route('admin.city.index');
    }

    public function destroy($id)
    {
        $l = City::findOrFail($id);
        $l->delete();
        return redirect()->back();
    }


}
