<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarTravel;
use App\Models\CarTravelOrder;
use App\Models\CarTravelPlace;
use App\Models\CarTravelPlaceOrder;
use App\Models\Company;
use App\Models\LodgerCar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LodgerController extends Controller
{

    // Возвращать список машин по выбранной компании
    public function getCarsList($company_id)
    {
        $company = Company::findOrFail($company_id);
        return response()->json($company->cars);
    }

    public function fixSelectedCarsForMe(Request $request)
    {
        $data = $request->all();
        $user = User::findOrFail($data['user_id']);
        foreach($data['cars'] as $car_id){
            $user->lodger_cars->attach($user->id, ['car_id' => $car_id]);
        }

        return response()->json('Cars successfully added');
    }

    public function getSelectedCarsList($user_id)
    {
        $user = User::findOrFail($user_id);
        return response()->json($user->lodger_cars);
    }

    public function getAllPlacesForRoute($car_travel_id)
    {
        $car_travel = CarTravel::findOrFail($car_travel_id);
        return response()->json($car_travel->get_all_places);
    }

    public function ticketSelling(Request $request, $car_travel_id)
    {
        $data = $request->all();
        $user_id = $data['user_id'];
        $place_number = $data['place_number'];
        $first_name = $data['first_name'];
        $phone = str_replace(' ', '', $data['phone']);
        $iin = $data['iin'];

        if ($this->checkingForDoubleIin($car_travel_id, $iin)) {
            return response()->json("С таким $iin уже продано билет. Укажите другой ИИН", 409);
        }

        if ($this->checkingForPhone($car_travel_id, $phone)) {
            return response()->json("В одном поездке может только 4 раза повторяется телефон. Укажите другой", 409);
        }

        DB::beginTransaction();

        try {
            $car_travel = CarTravel::findOrFail($car_travel_id);

            $car_travel_order = new CarTravelOrder();
            $car_travel_order->car_travel_id = $car_travel->id;
            $car_travel_order->driver_id = Car::find($car_travel->car_id)->user_id;
            $car_travel_order->passenger_id = $user_id;
            $car_travel_order->from_station_id = $car_travel->from_station_id;
            $car_travel_order->to_station_id = $car_travel->to_station_id;
            $car_travel_order->status = 'take';
            $car_travel_order->save();


            $car_travel_place = CarTravelPlace::where('car_travel_id', $car_travel_id)->where('number', $place_number)->first();

            if ($car_travel_place) {
                if ($car_travel_place->status == 'take') {
                    return response()->json("Место #$place_number уже забронирован", 400);
                }

                if ($car_travel_place->status == 'in_process') {
                    return response()->json("Место #$place_number уже забронирован", 400);
                }

                $car_travel_place->car_travel_order_id = $car_travel_order->id;
                $car_travel_place->status = 'take';
                $car_travel_place->passenger_id = $user_id;
                $car_travel_place->save();
            } else {
                return response()->json("Место не найдено", 400);
            }

            $car_travel_place_order = new CarTravelPlaceOrder();
            $car_travel_place_order->price = $car_travel_place->price;
            $car_travel_place_order->car_travel_id = $car_travel_place->car_travel_id;
            $car_travel_place_order->driver_id = $car_travel_place->driver_id;
            $car_travel_place_order->passenger_id = $user_id;
            $car_travel_place_order->number = $car_travel_place->number;
            $car_travel_place_order->from_station_id = $car_travel_place->from_station_id;
            $car_travel_place_order->to_station_id = $car_travel_place->to_station_id;
            $car_travel_place_order->car_travel_order_id = $car_travel_order->id;
            $car_travel_place_order->first_name = $first_name;
            $car_travel_place_order->phone = $phone;
            $car_travel_place_order->iin = $iin;
            $car_travel_place_order->status = 'take';
            $car_travel_place_order->save();

            DB::commit();

            return response()->json('Место успешно продано');

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json('Server error. Please try again', 500);
        }
    }

    public function checkingForDoubleIin($car_travel_id, $iin)
    {
        $result = CarTravelPlaceOrder::where(['car_travel_id' => $car_travel_id, 'iin' => $iin])->first();
        return ($result) ? true : false;
    }

    public function checkingForPhone($car_travel_id, $phone)
    {
        $result = CarTravelPlaceOrder::where(['car_travel_id' => $car_travel_id, 'phone' => $phone])->get();
        return (count($result) >= 4) ? true : false;
    }
}
