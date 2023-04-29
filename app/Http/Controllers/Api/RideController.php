<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\User;
use App\Packages\Firebase;
use App\Models\UserTravel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function lists(Request $request)
    {
        $user = $request['user'];
        //$rides = $user->rides;
        $rides = Ride::where(['user_id' => $user->id])
                ->selectRaw('rides.*, from_city.name as from_city_name, to_city.name as to_city_name')
                ->join('cities as from_city', 'from_city.id', 'rides.from_city_id')
                ->join('cities as to_city', 'to_city.id', 'rides.to_city_id')
                ->get();
        return response()->json($rides);
    }

    public function order(Request $request)
    {
        $data = $request->all();
        Ride::create($data);

        $user_travel_notices = UserTravel::where(['from_city_id' => $data['from_city_id'], 'to_city_id' => $data['to_city_id']])->get();
        if(count($user_travel_notices) > 0) {
            foreach ($user_travel_notices as $user_travel_notice) {
                /*Firebase::sendMultiple(User::where('id', $user_travel_notice->user_id)
                    ->where('push', 1)
                    ->select('device_token')
                    ->pluck('device_token')
                    ->toArray(), [
                    'title' => 'Saparline',
                    'body' => $user_travel_notice->from_city->name . " -> " . $user_travel_notice->to_city->name . " новые публикация",
                    'type' => 'driver_notice',
                ]);*/

                Firebase::sendMultiple(User::where('id', 4245)
                    ->where('push', 1)
                    ->select('device_token')
                    ->pluck('device_token')
                    ->toArray(), [
                    'title' => 'Saparline',
                    'body' => "место забронировано",
                    'type' => 'driver_confirmation',
                    'user_id' => $user_travel_notice->user_id,
                ]);
            }
        }

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
        $from_city_id = (isset($data['from_city_id'])) ? $data['from_city_id'] : null;
        $to_city_id = (isset($data['to_city_id'])) ? $data['to_city_id'] : null;
        $departure_date = (isset($data['departure_date'])) ? $data['departure_date'] : null;
        //$departure_time = $data['departure_time'];

        $rides = Ride::where(['status' => 'not'])
            ->with('user', 'from_city', 'to_city');

        $rides = (!is_null($from_city_id)) ? $rides->where(['from_city_id' => $from_city_id]) : $rides;

        $rides = (!is_null($to_city_id)) ? $rides->where(['to_city_id' => $to_city_id]) : $rides;

        $rides = (!is_null($departure_date)) ? $rides->whereDate('departure_date', '=' ,$departure_date) : $rides;

        return response()->json($rides->get());
    }
}
