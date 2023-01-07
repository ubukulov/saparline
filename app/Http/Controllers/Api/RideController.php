<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
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
}
