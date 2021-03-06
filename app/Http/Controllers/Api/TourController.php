<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarType;
use App\Models\Cashier;
use App\Models\City;
use App\Models\MeetPlace;
use App\Models\RestPlace;
use App\Models\Tour;
use App\Models\TourImage;
use App\Models\TourOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    public function getTours()
    {
        $tours = Tour::whereDate('tours.departure_time', '>=', Carbon::today()->toDateString())
            ->with('city', 'resting_place', 'meeting_place', 'car', 'images', 'orders')
            ->orderBy('departure_time')
            ->get();
        $items = collect();
        foreach ($tours as $tour) {
            $tour['stats'] = $tour->getOrderStats();
            $items->push($tour);
        }
        return response()->json($items, 200, ['charset' => 'utf-8'],JSON_UNESCAPED_UNICODE);
    }

    public function tourDestroy($tour_id)
    {
        $tour = Tour::findOrFail($tour_id);
        foreach($tour->images as $item) {
            $path = public_path() . '/' .$item->image;
            if(file_exists($path)) {
                unlink($path);
            }
        }

        Tour::destroy($tour_id);

        return response()->json('Success');
    }

    public function getInformationAboutTour($tour_id)
    {
        $tour = Tour::whereId($tour_id)
            ->with('city', 'resting_place', 'meeting_place', 'car', 'images', 'orders')
            ->first();
        $tour['stats'] = $tour->getOrderStats();
        $tour['cars'] = $tour->getCars();
        return response()->json($tour, 200, ['charset' => 'utf-8'],JSON_UNESCAPED_UNICODE);
    }

    # Поиск тура
    public function searchingTour(Request $request)
    {
        $data = $request->all();
        $city_id = $data['city_id'];
        $resting_place_id = $data['resting_place_id'];
        $departure_time = $data['departure_time'];

        $tours = Tour::where(['city_id' => $city_id, 'resting_place_id' => $resting_place_id])
                ->with('city', 'resting_place', 'meeting_place', 'car', 'images')
                ->whereDate('departure_time', '=', $departure_time)
                ->get();

        if (count($tours) == 0) {
            $year = (int) date('Y', strtotime($departure_time));
            $month = (int) date('m', strtotime($departure_time));
            $day = (int) date('d', strtotime($departure_time));
            $carbon = Carbon::create($year, $month, $day);

            $tours = Tour::where(['city_id' => $city_id, 'resting_place_id' => $resting_place_id])
                ->with('city', 'resting_place', 'meeting_place', 'car', 'images')
                ->whereDate('departure_time', '>=', $departure_time)
                ->whereDate('departure_time', '<=', $carbon->addDays(9)->toDateString())
                ->orderBy('departure_time')
                ->get();

            if(count($tours) > 0) {
                return response()->json("Самый ближайший тур есть на: " . date('d.m.Y', strtotime($tours[0]->departure_time)),400,
                    ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json("Туры не найдено. Скоро появиться:)",400,
                    ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            foreach($tours as $tour) {
                $tour['countFreePlaces'] = $tour->getCountFreePlaces();
                $tour['cars'] = $tour->getCars();
            }
        }

        return response()->json($tours);
    }

    public function getRestingPlaces($city_id)
    {
        $resting_places = RestPlace::where(['city_id' => $city_id])->get();
        return response()->json($resting_places);
    }

    public function getMeetingPlaces($city_id)
    {
        $meeting_places = MeetPlace::where(['city_id' => $city_id])->get();
        return response()->json($meeting_places);
    }

    public function uploadPreview(Request $request)
    {
        foreach($request['file'] as $img) {
            $this->uploadFile($img, 'tours');
        }

        return response()->json('success');
    }

    public function tourCreate(Request $request)
    {
        $data = $request->except('images');
        $data['departure_time'] = Carbon::create($data['departure_time'])->format('Y-m-d H:i:s');
        $data['destination_time'] = Carbon::create($data['destination_time'])->format('Y-m-d H:i:s');
        $resting_place = RestPlace::findOrFail($data['resting_place_id']);
        $data['title'] = $resting_place->title;
        DB::beginTransaction();
        try {
            $tour = Tour::create($data);

            foreach($request['images'] as $img) {
                $filename = $this->uploadFile($img, 'tours');
                TourImage::create([
                    'tour_id' => $tour->id, 'image' => $filename
                ]);
            }

            $car = Car::findOrFail($tour->car_id);
            $carType = CarType::find($car->car_type_id);

            for ($i = 1; $i <= $carType->count_places; $i++){
                TourOrder::create([
                    'tour_id' => $tour->id, 'number' => $i, 'status' => 'free', 'car_id' => $car->id
                ]);
            }

            DB::commit();

            return response()->json('Success');
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json("Server error: ".$exception, 500);
        }
    }

    public function getAllPlacesForTour($tour_id)
    {
        /*$tour_places = TourOrder::where(['tour_id' => $tour_id])
            ->get();
        return response()->json($tour_places);*/

        /*$rules = [
            'tour_id' => 'required|exists:tours,id',
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 400,
                ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }*/
        $tour = Tour::where('id',$tour_id)->with('car', 'resting_place', 'meeting_place', 'city')->first();
        $data['tour'] = $tour;
        //$data['places'] = TourOrder::where(['tour_id' => $tour_id])->get();
        $places = TourOrder::where(['tour_id' => $tour_id])->get();
        $arr = [];
        foreach($places as $place){
            if(array_key_exists($place->car_id, $arr)) {
                $arr[$place->car_id][] = $place;
            } else {
                $arr[$place->car_id][] = $place;
            }
        }

        $newArr = [];
        foreach($arr as $key=>$item){
            $newArr[] = [
                'car_id' => $key,
                'countFreePlaces' => $tour->getCountFreePlacesByCar($key),
                'places' => $item
            ];
        }

        $data['places'] = $newArr;

        return response()->json($data, 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function getSoldTicketsForCurrentTour($tour_id)
    {
        $tour_places = TourOrder::where(['tour_id' => $tour_id, 'status' => 'take'])
            ->get();
        return response()->json($tour_places);
    }

    public function tourReservation(Request $request, $tour_id)
    {
        $rules = [
            //'tour_id' => 'required|exists:tours,id',
            'places' => 'required|array|between:1,4',
            'car_id' => 'required|exists:cars,id'
        ];
        $data = $request->all();
        /*$place_number = $data['place_number'];
        $first_name = $data['first_name'];
        $phone = str_replace(' ', '', $data['phone']);
        $iin = $data['iin'];
        $passenger_id = (isset($data['passenger_id'])) ? $data['passenger_id'] : null;*/

        /**
        $request['places'] = [
            [
                'first_name' => 'User1',
                'phone' => '7772225522',
                'iin' => '887745210221',
                'place_number' => 15,
                'agent_id' => 15,
                'passenger_id' => 15,
            ],
            [
                'first_name' => 'User2',
                'phone' => '7772225222',
                'iin' => '887745210231',
                'place_number' => 18,
                'agent_id' => 15,
                'passenger_id' => 15,
            ],
            [
                'first_name' => 'User2',
                'phone' => '7772225222',
                'iin' => '887745210241',
                'place_number' => 16,
                'agent_id' => 15,
                'passenger_id' => 15,
            ]
        ];
         **/

        $messages = [];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 400, ['charset' => 'utf-8'],
                JSON_UNESCAPED_UNICODE);
        }


        $tour = Tour::findOrFail($tour_id);

        foreach ($data['places'] as $item) {
            if(gettype($item) == 'string') {
                $str = substr($item,1);
                $str = substr($str,0, strlen($str)-1);
                $arr = explode(',', $str);
                $arr1 = [];
                foreach($arr as $tt){
                    $ss = explode('=>', $tt);
                    $arr1[str_replace("'",'', trim($ss[0]))] = str_replace("'", '', trim($ss[1]));
                }

                $item = $arr1;
            }

            $place_number = $item['place_number'];
            $first_name = $item['first_name'];
            $phone = str_replace(' ', '', $item['phone']);
            $iin = $item['iin'];
            $passenger_id = (isset($item['passenger_id'])) ? $item['passenger_id'] : null;
            $agent_id = (isset($item['agent_id'])) ? $item['agent_id'] : null;

            if ($this->checkingForDoubleIin($tour_id, $iin)) {
                return response()->json("С таким $iin уже продано билет. Укажите другой ИИН", 409, ['charset' => 'utf-8'],
                    JSON_UNESCAPED_UNICODE);
            }

            if ($this->checkingForPhone($tour_id, $phone)) {
                return response()->json("В одном поездке может только 4 раза повторяется телефон. Укажите другой", 409, ['charset' => 'utf-8'],
                    JSON_UNESCAPED_UNICODE);
            }

            $tourOrder = TourOrder::where(['tour_id' => $tour_id, 'number' => $place_number, 'car_id' => $data['car_id']])->first();
            if($tourOrder){
                if($tourOrder->status == 'in_process'){
                    return response()->json("Место уже забронирован", 409, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
                }

                if($tourOrder->status == 'take'){
                    return response()->json("Место продано", 409, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
                }

                if($tourOrder->status == 'free'){
                    $tourOrder->passenger_id = $passenger_id;
                    $tourOrder->agent_id = $agent_id;
                    $tourOrder->status = 'in_process';
                    $tourOrder->first_name = $first_name;
                    $tourOrder->phone = $phone;
                    $tourOrder->iin = $iin;
                    if(is_null($passenger_id)) {
                        $tourOrder->price = $tour->seat_price;
                    } else {
                        $tourOrder->price = $tour->tour_price;
                    }
                    $tourOrder->booking_time = Carbon::now();
                    $tourOrder->save();
                }
            } else {
                return response()->json("Данные не найдены", 404, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }

        return response()->json("success", 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function checkingForDoubleIin($tour_id, $iin)
    {
        $result = TourOrder::where(['tour_id' => $tour_id, 'iin' => $iin])->first();
        return ($result) ? true : false;
    }

    public function checkingForPhone($tour_id, $phone)
    {
        $result = TourOrder::where(['tour_id' => $tour_id, 'phone' => $phone])->get();
        return (count($result) >= 4) ? true : false;
    }

    public function getFreePlacesForBooking($tour_id, $count)
    {
        $tour = Tour::findOrFail($tour_id);
        return response()->json($tour->getFreePlaceForBooking($count));
    }

    public function bookingByTourCompany(Request $request, $tour_id)
    {
        $data = $request->all();
        $tour = Tour::findOrFail($tour_id);
        foreach($data['listBookingPlaces'] as $item) {
            $item = json_decode($item);
            $order = TourOrder::findOrFail($item->id);
            $item->status = 'in_process';
            $item->price = $tour->seat_price;
            $item->booking_time = Carbon::now();
            $order->update((array)$item);
        }
        return response()->json('success');
    }

    public function getCities()
    {
        $cities = City::where('id', 1)->get();
        return response()->json($cities);
    }

    public function getAgency()
    {
        $agencies = Cashier::where(['type_id' => 2])->get();
        return response()->json($agencies);
    }

    public function getMyTickets($user_id)
    {
        $places = TourOrder::join('tours', 'tour_orders.tour_id', 'tours.id')
            ->join('cities','cities.id','tours.city_id')
            ->join('resting_places','resting_places.id','tours.resting_place_id')
            ->join('cars', 'cars.id', 'tour_orders.car_id')
            ->join('car_types','cars.car_type_id','car_types.id')
            ->join('users as driver','driver.id','cars.user_id')
            ->leftJoin('users as passenger','passenger.id','tour_orders.passenger_id')
            ->leftJoin('users as agent','agent.id','tour_orders.agent_id')

//            ->join('stations as from_station', 'from_station.id', 'car_travel.from_station_id')
//            ->join('cities as from_city', 'from_city.id', 'from_station.city_id')
//            ->join('stations as to_station', 'to_station.id', 'car_travel.to_station_id')
//            ->join('cities as to_city', 'to_city.id', 'to_station.city_id')
//            ->join('cars', 'car_travel.car_id', 'cars.id')
//            ->join('car_types', 'cars.car_type_id', 'car_types.id')
//            ->join('users', 'users.id', 'car_travel_place_orders.driver_id')
            ->where('tour_orders.passenger_id', $user_id)
            ->whereRaw("tours.destination_time > CURRENT_TIMESTAMP()")
            ->whereIn('tour_orders.status', ['take', 'in_process'])
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
                'driver.phone as phone_number',
                'driver.bank_card',
                'driver.card_fullname',
                'tour_orders.first_name',
                'tour_orders.phone',
                'tour_orders.iin'
            )
            ->orderBy('tour_orders.id', 'desc')
            ->get();

        return response()->json($places, 200, ['charset' => 'utf-8'],JSON_UNESCAPED_UNICODE);
    }

    public function cancelTicket(Request $request)
    {
        $data = $request->all();
        /*$rules = [
            'tour_id' => 'required|exists:tours,id',
            'number' => 'required',
            'reason_for_return' => 'required',
            'car_id' => 'required|exists:cars,id'
        ];*/

        $rules = [
            'place_id' => 'required|exists:tour_orders,id',
            'reason_for_return' => 'required',
        ];
        $messages = [
        ];
        $validator = $this->validator($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 400, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }

        //$tourOrder = TourOrder::where(['tour_id' => $data['tour_id'], 'number' => $data['number'], 'car_id' => $data['car_id']])->first();
        $tourOrder = TourOrder::findOrFail($data['place_id']);
        if($tourOrder) {
            $tourOrder->reason_for_return = $data['reason_for_return'];
            $tourOrder->status = 'cancel';
            $tourOrder->save();

            return response()->json("Success", 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json("Не найден запись", 400, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getListOtherCars($tour_id)
    {
        $tour = Tour::findOrFail($tour_id);
        $car = Car::findOrFail($tour->car_id);
        $otherCars = Car::where(['cars.is_confirmed' => 1, 'cars.car_type_id' => $car->car_type_id])
            ->where('cars.id', '!=', $car->id)
            ->get();

        return response()->json($otherCars);
    }

    public function addOtherCar(Request $request)
    {
        $tour_id = $request->input('tour_id');
        $car_id = $request->input('car_id');

        $tour = Tour::findOrFail($tour_id);
        $car = Car::findOrFail($car_id);
        $carType = CarType::find($car->car_type_id);

        for ($i = 1; $i <= $carType->count_places; $i++){
            TourOrder::create([
                'tour_id' => $tour->id, 'number' => $i, 'status' => 'free', 'car_id' => $car->id
            ]);
        }

        return response()->json('success');
    }
}
