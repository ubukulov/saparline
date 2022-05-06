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

    public function returnTickets()
    {
        $data['tours'] = TourOrder::join('tours','tour_orders.tour_id','tours.id')
            //->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('cities','cities.id','tours.city_id')
            ->join('resting_places','resting_places.id','tours.resting_place_id')
            //->join('cities as to_city','to_city.id','to_station.city_id')
            ->join('cars','tours.car_id','cars.id')
            ->join('car_types','cars.car_type_id','car_types.id')
            ->join('users as driver','driver.id','cars.user_id')
            ->leftJoin('users as passenger','passenger.id','tour_orders.passenger_id')
            ->where('tour_orders.status',"cancel")
            ->whereDate('tours.departure_time', '>=', date('Y-m-d'))
            ->select(
                'tour_orders.id',
                'tour_orders.number',
                'tour_orders.price',
                'tour_orders.status',
                'tours.departure_time',
                'tours.destination_time',
                'cities.name as from_city',
                //'from_station.name as from_station',
                'resting_places.title as to_city',
                //'to_station.name as to_station',
                'cars.state_number as car_state_number',
                'car_types.name as car_type',
                'car_types.count_places as car_type_count_places',
                'driver.name as driver_name',
                'driver.phone as driver_phone',
                'passenger.name as passenger_name',
                'passenger.phone as passenger_phone',
                'tour_orders.reason_for_return'
            )
            ->orderBy('tour_orders.booking_time','desc')
            ->paginate(15);

        return view('admin.tour.return_tickets', $data);
    }

    public function cancelOrder($id)
    {
        $order = TourOrder::findOrFail($id);

        if (!is_null($order->passenger_id)) {
            Firebase::sendMultiple(User::where('id',$order->passenger_id)
                ->where('push',1)
                ->select('device_token')
                ->pluck('device_token')
                ->toArray(),[
                'title' => 'Saparline',
                'body' => "Вы вернули билет!",
                'type' => 'cancel',
                'user_id' => $order->passenger_id,
            ]);
        }

        $order->status = 'free';
        $order->passenger_id = null;
        $order->agent_id = null;
        $order->price = null;
        $order->first_name = null;
        $order->phone = null;
        $order->iin = null;
        $order->reason_for_return = null;
        $order->booking_time = null;
        $order->save();

        return redirect()->back();
    }
}
