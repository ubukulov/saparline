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
}
