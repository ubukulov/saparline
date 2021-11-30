<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashierResource;
use App\Models\Cashier;
use App\Models\CompanyCar;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use App\Models\City;
use App\Models\Travel;
use App\Models\CarTravel;
use App\Models\Station;
use App\Models\CarTravelStation;
use App\Models\CarType;
use App\Models\Car;
use App\Models\CarTravelPlace;
use App\Models\CarTravelOrder;
use App\Models\CarTravelPlaceOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Resources\TravelResource;
use App\Http\Resources\TravelResourceForCashier;


class CashierController extends Controller {

	public function login(Request $request)
	{
		$rules = [
            'email'=> 'required',
            'password'=> 'required',
        ];

        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
		$email = $request->input('email');
		$password = $request->input('password');
		$cashier = Cashier::where(['email' => $email])->first();

		if (!$cashier){
            return response()->json('Неверные данные',404);
        }

        if (!Hash::check($password, $cashier->password)){
            return response()->json('Неверные данные',400);
        }
		
		if ($cashier->active == 0) {
			return response()->json('Пользователь еще не активирован', 409);
		}

		if($cashier) {
			return response()->json(['user' => new CashierResource($cashier)], 200);
		}
	}
	
	public function register(Request $request)
	{
		$rules = [
            'first_name'=> 'required',
            'type_id'=> 'required',
            'city_id'=> 'required',
            'station_id'=> 'required',
            'company_name'=> 'required',
            'phone'=> 'required',
            'email'=> 'required|unique:cashiers,email',
            'password'=> 'required',
        ];

        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
		
		$data = $request->all();
		$data['active'] = 0;
		$data['password'] = bcrypt($data['password']);
		$cashier = Cashier::create($data);
		if ($cashier) {
			return response()->json('success', 200);
		}
		
		return response()->json('Server error. Please try later', 500);
	}

	public function getCompanies()
	{
		return response()->json(Company::all());
	}

	public function getCompanyCarsList($company_id)
    {
        $company_cars = CompanyCar::where(['company_id' => $company_id])
			->selectRaw('cars.id, cars.state_number as number, car_types.name as car_type_name, car_types.count_places')
            ->join('cars', 'cars.id', '=', 'company_cars.car_id')
            ->join('car_types', 'car_types.id', '=', 'cars.car_type_id')
            ->get();
        return response()->json($company_cars);
    }
	
	public function getCities()
	{
		return response()->json(City::all());
	}
	
	public function getCityStationsList($city_id)
	{
		$city = City::findOrFail($city_id);
		return response()->json($city->stations);
	}
	
	public function createTripByCashier(Request $request)
	{
		$data = $request->all();
		$data['departure_time'] = Carbon::create($data['departure_time'])->format('Y-m-d H:i:s');
		$data['destination_time'] = Carbon::create($data['destination_time'])->format('Y-m-d H:i:s');
		
		DB::beginTransaction();
		
		try {
			$travel = Travel::join('cities as from_city','from_city.id','travel.from_city_id')
            ->join('cities as to_city','to_city.id','travel.to_city_id')
            ->join('stations as from_station' , 'from_station.city_id','from_city.id')
            ->join('stations as to_station' , 'to_station.city_id','to_city.id')
            ->where('from_station.id',$data['from_station_id'])
            ->where('to_station.id',$data['to_station_id'])
            ->select('travel.*')
            ->first();
			
			
			$carTravel = new CarTravel();

			$carTravel->from_station_id = $request['from_station_id'];
			$carTravel->to_station_id = $request['to_station_id'];
			$carTravel->departure_time = $data['departure_time'];
			$carTravel->destination_time = $data['destination_time'];
			$carTravel->travel_id = $travel ? $travel->id : null;
			$carTravel->from_city_id = $data['from_city_id'];
			$carTravel->to_city_id = $data['to_city_id'];
			$carTravel->car_id = $data['car_id'];
			$carTravel->save();
			
			if (Station::where('id',$data['from_station_id'])->exists()){
				$travelStation = new CarTravelStation();
				$travelStation->car_travel_id = $carTravel->id;
				$travelStation->station_id = $data['from_station_id'];
				$travelStation->save();
			} else{
				return response()->json('station_id error',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
			}
			
			if (Station::where('id',$data['to_station_id'])->exists()){
				$travelStation = new CarTravelStation();
				$travelStation->car_travel_id = $carTravel->id;
				$travelStation->station_id = $data['to_station_id'];
				$travelStation->save();
			} else{
				return response()->json('station_id error',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
			}
			
			
			
			$car = Car::findOrFail($data['car_id']);
			$carType = CarType::find($car->car_type_id);
			
			for ($i = 1 ; $i <= $carType->count_places; $i++){
				$travelPlace = new CarTravelPlace();
				$travelPlace->car_travel_id = $carTravel->id;
				$travelPlace->driver_id = $car->user_id;
				$travelPlace->number = $i;
				$travelPlace->from_station_id = $request['from_station_id'];
				$travelPlace->to_station_id = $request['to_station_id'];
				$travelPlace->save();
			}
			
		
			
			foreach (json_decode($request['price_places']) as $place_price) {
				CarTravelPlace::where('car_travel_id',$carTravel->id)
					->whereBetween('number',[$place_price->from,$place_price->to])
					->update([
						'price' => $place_price->price
					]);
			}
			
			DB::commit();
			
			return response()->json('success', 200);
		} catch (\Exception $exception) {
			DB::rollBack();
			return response()->json('Server error. Please try again', 500);
		}
	}
	
	public function travelUpcoming(){
        $travels = CarTravel::join('cars','car_travel.car_id','cars.id')
            ->whereRaw("car_travel.destination_time >= CURRENT_TIMESTAMP()")
            ->orderBy('car_travel.id','desc')
            ->select('car_travel.*')
            ->limit(100)
            ->get();
        return response()->json(TravelResource::collection($travels),200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
    }
	
	public function getCarInfo($car_id)
	{
		$car = Car::whereId($car_id)
					->with('car_type')
					->first();
		return response()->json($car);			
	}
	
	public function getTicketsForToday()
	{
		$travels = CarTravel::join('cars','car_travel.car_id','cars.id')
            ->selectRaw('car_travel.*')
            ->whereDate("car_travel.departure_time", Carbon::today()->toDateString())
            ->orderBy('car_travel.id','desc')
            ->limit(100)
            ->get();
			
		return response()->json(TravelResourceForCashier::collection($travels),200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
	}
	
	public function getAllPlacesForRoute($car_travel_id)
	{
		$car_travel = CarTravel::findOrFail($car_travel_id);
		return response()->json($car_travel->get_all_places);
	}
	
	public function ticketSelling(Request $request, $car_travel_id)
	{
		$data = $request->all();
		$place_number = $data['place_number'];
		$first_name = $data['first_name'];
		$phone = str_replace(' ', '', $data['phone']);
		$iin = $data['iin'];
		
		DB::beginTransaction();
		
		try {
			$car_travel = CarTravel::findOrFail($car_travel_id);
			
			$car_travel_order = new CarTravelOrder();
			$car_travel_order->car_travel_id = $car_travel->id;
			$car_travel_order->driver_id = Car::find($car_travel->car_id)->user_id;
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
                $car_travel_place->save();
			} else {
				return response()->json("Место не найдено", 400);
			}
			
			$car_travel_place_order = new CarTravelPlaceOrder();
			$car_travel_place_order->price = $car_travel_place->price;
			$car_travel_place_order->car_travel_id = $car_travel_place->car_travel_id;
			$car_travel_place_order->driver_id = $car_travel_place->driver_id;
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
	
	public function getSoldTicketsForToday($tomorrow = false)
	{
		
					
		if ($tomorrow) {
			$car_travel_sold_tickes_for_today = CarTravelPlaceOrder::where(['car_travel_place_orders.status' => 'take'])
					->selectRaw('car_travel_place_orders.*, cars.state_number, DATE_FORMAT(car_travel.departure_time, "%d.%m.%Y") as dep_date, DATE_FORMAT(car_travel.departure_time, "%H:%i") as dep_time, DATE_FORMAT(car_travel.destination_time, "%d.%m.%Y") as des_date, DATE_FORMAT(car_travel.destination_time, "%H:%i") as des_time')
					->with('driver', 'from_station', 'to_station')
					->join('car_travel','car_travel.id','car_travel_place_orders.car_travel_id')
					->join('cars','car_travel.car_id','cars.id')
					//->leftJoin('company_cars','company_cars.car_id','cars.id')
					//->leftJoin('companies', 'companies.id', 'company_cars.company_id')
					//->whereDate("car_travel.departure_time", '>', "CURDATE()")
					->orderBy('car_travel.id','desc')
					->limit(100)
					->get();
			//$car_travel_sold_tickes_for_today = $car_travel_sold_tickes_for_today->whereRaw("car_travel.departure_time > CURRENT_TIMESTAMP()")->get();
		} else {
			$car_travel_sold_tickes_for_today = CarTravelPlaceOrder::where(['car_travel_place_orders.status' => 'take'])
					->selectRaw('car_travel_place_orders.*, cars.state_number, DATE_FORMAT(car_travel.departure_time, "%d.%m.%Y") as dep_date, DATE_FORMAT(car_travel.departure_time, "%H:%i") as dep_time, DATE_FORMAT(car_travel.destination_time, "%d.%m.%Y") as des_date, DATE_FORMAT(car_travel.destination_time, "%H:%i") as des_time')
					->with('driver', 'from_station', 'to_station')
					->join('car_travel','car_travel.id','car_travel_place_orders.car_travel_id')
					->join('cars','car_travel.car_id','cars.id')
					//->leftJoin('company_cars','company_cars.car_id','cars.id')
					//->leftJoin('companies', 'companies.id', 'company_cars.company_id')
					//->whereDate("car_travel.departure_time", Carbon::today()->toDateString())
					->orderBy('car_travel.id','desc')
					->limit(100)
					->get();
			//$car_travel_sold_tickes_for_today = $car_travel_sold_tickes_for_today->whereDate("car_travel.departure_time", Carbon::today()->toDateString())->get();
		}		
					
		return response()->json($car_travel_sold_tickes_for_today);	
	}
	
	public function getSoldTickets()
	{
		$car_travel_sold_tickes_for_today = CarTravelPlaceOrder::where(['car_travel_place_orders.status' => 'take'])
					->selectRaw('car_travel_place_orders.*, cars.state_number, DATE_FORMAT(car_travel.departure_time, "%d.%m.%Y") as dep_date, DATE_FORMAT(car_travel.departure_time, "%H:%i") as dep_time, DATE_FORMAT(car_travel.destination_time, "%d.%m.%Y") as des_date, DATE_FORMAT(car_travel.destination_time, "%H:%i") as des_time')
					->with('driver', 'from_station', 'to_station')
					->join('car_travel','car_travel.id','car_travel_place_orders.car_travel_id')
					->join('cars','car_travel.car_id','cars.id')
					//->leftJoin('company_cars','company_cars.car_id','cars.id')
					//->leftJoin('companies', 'companies.id', 'company_cars.company_id')
					->orderBy('car_travel.id','desc')
					->limit(100)
					->get();
		
		return response()->json($car_travel_sold_tickes_for_today);		
	}
	
	public function getTicketsByFilter($filter_id)
	{
		switch((int)$filter_id) {
			case 0:
				return $this->getSoldTicketsForToday();
			break;
			
			case 1:
				return $this->getSoldTicketsForToday(true);
			break;
			
			case 2:
				return $this->getSoldTickets();
			break;
		}
	}
}
