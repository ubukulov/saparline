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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Resources\TravelResource;


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
	
	public function getTicketsForToday(Request $request)
	{
		$data = $request->all();
		
	}
}
