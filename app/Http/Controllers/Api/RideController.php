<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function lists(Request $request)
    {
        $user = $request['user'];
        $rides = $user->rides;
        return response()->json($rides);
    }

    public function order(Request $request)
    {
        $data = $request->all();
        Ride::create($data);
        return response('Success');
    }

    public function changeStatus(Request $request)
    {
        $status = $request->input('status');
        $ride_id = $request->input('ride_id');
        $ride = Ride::findOrFail($ride_id);
        if($ride->status == 'ok') {
            return response('status already changed', 406);
        }

        if($status == 'ok' && $ride->status == 'not') {
            $ride->status = $status;
            $ride->save();
            return response('status changed successfully', 200);
        }
    }

    public function deleteRide($ride_id)
    {
        $ride = Ride::find($ride_id);
        if($ride) {
            Ride::destroy($ride_id);
            return response('success', 200);
        } else {
            return response('already deleted', 403);
        }
    }

    public function updateRide(Request $request, $ride_id)
    {
        $ride = Ride::findOrFail($ride_id);
        $data = $request->all();
        $ride->update($data);
        return response('success');
    }

    public function getInfoForDriver(Request $request)
    {
        $data = $request->all();
        $from_city_id = $data['from_city_id'];
        $to_city_id = $data['to_city_id'];
        $departure_date = $data['departure_date'];
        //$departure_time = $data['departure_time'];

        $rides = Ride::where(['from_city_id' => $from_city_id, 'to_city_id' => $to_city_id, 'status' => 'not'])
                ->whereDate('departure_date', '=', $departure_date)
                ->get();

        return response()->json($rides);
    }
}
