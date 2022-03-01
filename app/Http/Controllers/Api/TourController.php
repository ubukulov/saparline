<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarType;
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
        $tours = Tour::whereDate('tours.departure_time', Carbon::today()->toDateString())
            ->with('city', 'resting_place', 'meeting_place', 'car', 'images', 'orders')
            ->get();
        $items = collect();
        foreach ($tours as $tour) {
            $tour['stats'] = $tour->getOrderStats();
            $items->push($tour);
        }
        return response()->json($items);
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
        return response()->json($tour);
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
                ->whereDate('departure_time', $departure_time)
                ->get();
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
                    'tour_id' => $tour->id, 'number' => $i, 'status' => 'free'
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
        $data['tour'] = Tour::find($tour_id);
        $data['places'] = TourOrder::where(['tour_id' => $tour_id])->get();

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
            'tour_id' => 'required|exists:tours,id',
            'places' => 'required|array|between:1,4',
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
            $place_number = $item['place_number'];
            $first_name = $item['first_name'];
            $phone = str_replace(' ', '', $item['phone']);
            $iin = $item['iin'];
            $passenger_id = (isset($item['passenger_id'])) ? $item['passenger_id'] : null;
            $agent_id = (isset($item['agent_id'])) ? $item['agent_id'] : null;

            if ($this->checkingForDoubleIin($tour_id, $iin)) {
                return response()->json("С таким $iin уже продано билет. Укажите другой ИИН", 409);
            }

            if ($this->checkingForPhone($tour_id, $phone)) {
                return response()->json("В одном поездке может только 4 раза повторяется телефон. Укажите другой", 409);
            }

            $tourOrder = TourOrder::where(['tour_id' => $tour_id, 'number' => $place_number])->first();
            if($tourOrder){
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
                    return response()->json("Место забронирован", 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
                }

                if($tourOrder->status == 'in_process'){
                    return response()->json("Место уже забронирован", 409, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
                }

                if($tourOrder->status == 'take'){
                    return response()->json("Место продано", 409, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
                }

            } else {
                return response()->json("Данные не найдены", 404, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        /*$tourOrder = TourOrder::where(['tour_id' => $tour_id, 'number' => $place_number])->first();
        if($tourOrder){
            if($tourOrder->status == 'free'){
                $tourOrder->passenger_id = $passenger_id;
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
                return response()->json("Место забронирован", 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }

            if($tourOrder->status == 'in_process'){
                return response()->json("Место уже забронирован", 409, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }

            if($tourOrder->status == 'take'){
                return response()->json("Место продано", 409, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }

        } else {
            return response()->json("Данные не найдены", 404, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }*/
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
}
