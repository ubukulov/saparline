<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourOrder;
use App\Models\User;
use App\Packages\Firebase;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index()
    {
        $tickets = TourOrder::where('tour_orders.status', 'in_process')
                ->select(
                    'tour_orders.*', 'car_types.id as car_type_id', 'cities.name as from_city',
                    'resting_places.title as to_city', 'tours.departure_time',
                    'tours.destination_time', 'driver.name as driver_name',
                    'driver.phone as driver_phone', 'cars.state_number as car_state_number'
                )
                ->join('tours', 'tours.id', 'tour_orders.tour_id')
                ->join('cities','cities.id','tours.city_id')
                ->join('resting_places','resting_places.id','tours.resting_place_id')
                ->join('cars', 'cars.id', 'tours.car_id')
                ->join('car_types','cars.car_type_id','car_types.id')
                ->join('users as driver','driver.id','cars.user_id')
                ->leftJoin('users as passenger','passenger.id','tour_orders.passenger_id')
                ->leftJoin('users as agent','agent.id','tour_orders.agent_id')
                ->orderBy('tour_orders.booking_time','desc')
                ->get();

        return view('admin.tour.index', compact('tickets'));
    }

    public function orderTake($ticketId)
    {
        $tourOrder = TourOrder::findOrFail($ticketId);
        $tourOrder->status = 'take';
        $tourOrder->save();

        if (!is_null($tourOrder->passenger_id)) {
            Firebase::sendMultiple(User::whereIn('id',[$tourOrder->passenger_id])->select('device_token')->pluck('device_token')->toArray('device_token'),[
                'title' => 'Saparline',
                'body' => "Место забронировано",
                'type' => 'place_take',
                'travel_place_id' => $tourOrder->number,
            ]);
        }

        return redirect()->back();
    }

    public function orderReject($ticketId)
    {
        $tourOrder = TourOrder::findOrFail($ticketId);
        $tourOrder->status = 'free';
        $tourOrder->passenger_id = null;
        $tourOrder->agent_id = null;
        $tourOrder->price = null;
        $tourOrder->first_name = null;
        $tourOrder->phone = null;
        $tourOrder->iin = null;
        $tourOrder->booking_time = null;
        $tourOrder->save();

        return redirect()->back();
    }
}
