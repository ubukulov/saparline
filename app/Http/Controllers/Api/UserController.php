<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TravelPlaceResource;
use App\Http\Resources\TravelResource;
use App\Models\CarTravel;
use App\Models\CarTravelOrder;
use App\Models\CarTravelPlace;
use App\Models\CarTravelPlaceOrder;
use App\Models\CarTravelStation;
use App\Models\CarType;
use App\Http\Controllers\Controller;

use App\Http\Resources\UserResource;
use App\Models\Car;
use App\Models\City;
use App\Models\Setting;
use App\Models\Station;
use App\Models\Travel;
use App\Models\TravelStation;
use App\Models\User;

use App\Packages\Firebase;
use App\Packages\SMS;
use Carbon\Carbon;
use Faker\Provider\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    function register(Request $request){
        $rules = [
            'phone'=> 'required|unique:users,phone|size:10',
            'password'=> 'required',
            'role' =>'in:passenger,driver'
        ];
        $messages = [
            'phone.required'=>'Введите телефон номер',
            'password.required'=>'Введите пароль',
            'phone.unique'=>'Телеофон номер уже занят',
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }


        $sms = \App\Models\Sms::wherePhone($request['phone'])->whereType('register')->whereRaw('created_at > date_sub(now(), INTERVAL 1 minute)')->first();
        if ($sms){
            return response()->json("Смс код уже отправлен",400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $code = rand(1000,9999);
        $sms = new \App\Models\Sms();
        $sms->phone = $request['phone'];
        $sms->code = $code;
        $sms->password = $request['password'];
        $sms->role = $request['role'];
        $sms->message = $message = "kod podtverzhdeniya: $code";
        $sms->type = 'register';
        $sms->save();


        SMS::send('+7'.$request['phone'],'Saparline, '.$message);

        return response()->json("на ваше номер отправлять смс код подтверждения",200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }
    function phoneConfirmation(Request $request){
        $rules = [
            'phone'=> 'required|unique:users,phone|size:10',
            'code'=> 'required',
        ];
        $messages = [
            'phone.required'=>'Введите телефон номер',
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $sms = \App\Models\Sms::where('phone',$request['phone'])
            ->where('code',$request['code'])
            ->where('type','register')
            ->orderBy('id','desc')
            ->first();
        if (!$sms){
            return response()->json('неверный код подтверждения',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $user = new User();
        $user->phone = $request['phone'];
        $user->password = bcrypt($sms->password);
        $user->role = $sms->role;
        $user->token = Str::random(30);
        $user->device_token = $request['device_token'];
        $user->save();


        return response()->json(['token'=>$user->token,'user'=>new UserResource($user)],200);
    }
    function confirmation(Request $request){
        $user = $request['user'];

        if ( $user->confirmation == 'waiting'){
            return response()->json("Вы уже отправили заявку",400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

//        if ( $user->confirmation == 'reject'){
//            return response()->json("Вам отказано в доступе",400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
//        }

        if ($user->role == 'passenger'){
            $rules = [
                'name'=> 'required',
                'surname'=> 'required',
                'avatar' =>'image',
                'code' =>'numeric',
            ];
        }else{
            $rules = [
                'name'=> 'required',
                'avatar' =>'image',
                'passport_image' =>'required|image',
                'identity_image' =>'required|image',
                'car_image' =>'required|image',
                'state_number' =>'required',
                'car_type_id' =>'required|exists:car_types,id',
                'code' =>'numeric',
                'tv'=> 'required|in:1,0',
                'conditioner'=> 'required|in:1,0',
                'baggage'=> 'required|in:1,0',
            ];
        }

        $messages = [

        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }



        $user->name = $request['name'];
        $user->device_token = $request['device_token'];
        $user->avatar = $this->uploadFile($request['avatar'],'images/avatars');

        if ($user->role == 'driver'){
            $user->passport_image = $this->uploadFile($request['passport_image'],'images/passport');
            $user->identity_image = $this->uploadFile($request['identity_image'],'images/identity');
            $user->confirmation = 'waiting';
        }
        $user->save();

        if ($user->role == 'driver'){
           if (!Car::where('user_id',$user->id)->exists()){
               $car = new Car();
               $car->user_id = $user->id;
               $car->car_type_id = $request['car_type_id'];
               $car->state_number = $request['state_number'];
               $car->image = $this->uploadFile($request['car_image'],'car_images');
               $car->tv = $request['tv'] ;
               $car->conditioner = $request['conditioner'];
               $car->baggage = $request['baggage'];
               $car->save();
           }
        }
        return response()->json(new UserResource($user),200);
    }
    function login(Request $request){
        $rules = [
            'phone'=> 'required',
            'password'=> 'required',
//            'device_token'=> 'required'
        ];

        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $user = User::where('phone',$request['phone'])->first();

        if (!$user){
            return response()->json('Неверные данные',404,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        if (!Hash::check($request['password'],$user->password)){
            return response()->json('Неверные данные',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }


        if ($request['device_token']){
            $user->device_token = $request['device_token'];
        }
        $user->save();

        return response()->json(['token'=>$user->token,'user'=>new UserResource($user)],200);
    }
    function logout(Request $request){
        $user = $request['user'];
        $user->device_token = null;
        $user->save();

        return response()->json('logout',200);
    }
    function loginByToken(Request $request){



        if (!$request->hasHeader('token')){
            return response()->json('token required',400);
        }

        $user = User::whereToken($request->header('token'))->first();
        return response()->json(new UserResource($user),200);
    }
    function getById(Request $request){
        $rules = [
            'id'=> 'required|exists:users,id',
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $user = User::find($request['id']);

        return response()->json(new UserResource($user),200);
    }
    function edit(Request $request){
        $rules = [
            'avatar'=> 'image',
            'push' => 'in:0,1',
            'sound' => 'in:0,1',
            'lang' => 'in:ru,kz',
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $user = $request['user'];

        if ($request['name']){
            $user->name = $request['name'];
        }
        if ($request['surname']){
            $user->surname = $request['surname'];
        }

        if ($request['phone']){
            if (User::where('id','<>',$user->id)->where('phone',$request['phone'])->exists()){
                return response()->json('Телефон номер занять',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
            }
            $user->phone = $request['phone'];
        }
        if ($request['avatar']){
            $this->deleteFile($user->avatar);
            $user->avatar = $this->uploadFile($request['avatar'],'avatars/user_'.$user->id);
        }
        if ($request['password_old'] and $request['password_new']){
            if (!Hash::check($request['password_old'],$user->password)){
                return response()->json('Неверный пароль',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
            }
            $user->password = bcrypt($request['password_new']);
        }
        if (isset($request['push'])){
            $user->push = $request['push'];
        }
        if (isset($request['sound'])){
            $user->sound = $request['sound'];
        }
        if (isset($request['lang'])){
            $user->lang = $request['lang'];
        }


        $user->save();

        return response()->json(new UserResource($user),200);
    }

    function search(Request $request){
        $rules = [
            'search' => 'required|max:50',
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $users = User::where('name','LIKE',"%$request->search%")
            ->orWhere('surname','LIKE',"%$request->search%")
            ->select('id','name','surname','avatar')->limit(100)->get();

        return response()->json($users,200);


    }
    function roleDriver(Request $request){
        $user = $request['user'];
        switch ($user->confirmation){
            case "confirm":
                $user->role = 'driver';
                $user->save();

                return response()->json(['message' => 'Вы теперь водитель','user' => new UserResource($user)],200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);


            case "waiting":
                return response()->json([
                    'message' => 'Ожидайте,Админ проверяет ваши данные',
                    'user' => new UserResource($user)
                ],202,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
            case "reject":
                return response()->json([
                    'message'=>'Админ отклонил ваш запрос',
                    'user' => new UserResource($user)
                ],203,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
            default:
                $rules = [
                    'passport_image' => 'required|image',
                    'identity_image' => 'required|image',
                    'car_image' => 'required|image',
                    'state_number' => 'required',
                    'car_type_id' => 'required|exists:car_types,id',
                    'tv'=> 'required|in:1,0',
                    'conditioner'=> 'required|in:1,0',
                    'baggage'=> 'required|in:1,0',
                ];


                $messages = [

                ];
                $validator = $this->validator($request->all(), $rules, $messages);
                if ($validator->fails()) {
                    return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
                }

                $user->passport_image = $this->uploadFile($request['passport_image'],'images/passport');
                $user->identity_image = $this->uploadFile($request['identity_image'],'images/identity');
                $user->confirmation = 'waiting';
                $user->role = 'driver';
                $user->save();

                if (!Car::where('user_id',$user->id)->exists()){
                    $car = new Car();
                    $car->user_id = $user->id;
                    $car->car_type_id = $request['car_type_id'];
                    $car->state_number = $request['state_number'];
                    $car->image = $this->uploadFile($request['car_image'],'car_images');
                    $car->tv = $request['tv'] ;
                    $car->conditioner = $request['conditioner'];
                    $car->baggage = $request['baggage'];
                    $car->save();
                }
                return response()->json([
                    'message'=>'Запрос отправлен Админу',
                    'user' => new UserResource($user)
                ],201,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }



    }
    function rolePassenger(Request $request){
        $user = $request['user'];
        $user->role = 'passenger';
        $user->save();
        return response()->json("Вы теперь пассажир",200);
    }

    function passwordResetSend(Request $request){
        $rules = [
            'phone'=> 'required|exists:users,phone|size:10',
        ];
        $messages = [
            'phone.required'=>'Введите телефон номер',
            'password.required'=>'Введите пароль',
            'phone.unique'=>'Телеофон номер уже занят',
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }



        $code = rand(1000,9999);
        $sms = new \App\Models\Sms();
        $sms->phone = $request['phone'];
        $sms->code = $code;
        $sms->message = $message = "kod podtverzhdeniya: $code";
        $sms->type = 'reset_password';
        $sms->save();


        SMS::send('+7'.$request['phone'],'Saparline, '.$message);

        return response()->json("на ваше номер отправлять смс код подтверждения",200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }
    function passwordResetCheck(Request $request){
        $rules = [
            'phone'=> 'required|exists:users,phone',
            'code'=> 'required',
            'password'=> 'required',
        ];
        $messages = [
            'phone.required'=>'Введите телефон номер',
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $sms = \App\Models\Sms::where('phone',$request['phone'])
            ->where('code',$request['code'])
            ->where('type','reset_password')
            ->orderBy('id','desc')
            ->first();
        if (!$sms){
            return response()->json('неверный код подтверждения',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $user = User::where('phone',$request['phone'])->first();
        $user->password = bcrypt($request['password']);
        $user->token = Str::random(30);
        $user->save();

        return response()->json(['token'=>$user->token,'user'=>new UserResource($user)],200);
    }

    function setting(){
        return response()->json(Setting::first());
    }
    function cities(){
        return response()->json(City::all());
    }
    function stations(Request $request){
        $rules = [
            'city_id' => 'required|exists:cities,id',
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }


        return response()->json(Station::where('city_id',$request['city_id'])->get());
    }
    function carTypes(Request $request){
        return response()->json(CarType::all());
    }

    //Driver
    function travelAdd(Request $request){
        $rules = [
            'from_station_id' => 'required|exists:stations,id',
            'to_station_id' => 'required|exists:stations,id',
            'departure_time'=> 'required|date_format:Y-m-d H:i:s',
            'destination_time'=> 'required|date_format:Y-m-d H:i:s',
            'stations' => 'array',
            'times' => 'array',
            "place_price" => 'required|array',
        ];
        $messages = [
            "time.date_format" => 'Неверный формат time'
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $user = $request['user'];
        $car = Car::where('user_id',$user->id)->first();
        if ($request['user']->role != 'driver'){
            return response()->json('role error',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $countPlace = 0;
        foreach ($request['place_price'] as $place_price) {
            if ($place_price['from'] >= $place_price['to']){
                return response()->json('Неверные данные',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
            }

            $countPlace = $countPlace + ($place_price['to'] - $place_price['from'] + 1);
        }
        if ($countPlace  != CarType::find($car->car_type_id)->count_places){
            return response()->json('Неверные данные,введите цен всех мест',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }


        function add ($request,$departure_time,$destination_time){

            $travel = Travel::join('cities as from_city','from_city.id','travel.from_city_id')
            ->join('cities as to_city','to_city.id','travel.to_city_id')
            ->join('stations as from_station' , 'from_station.city_id','from_city.id')
            ->join('stations as to_station' , 'to_station.city_id','to_city.id')
            ->where('from_station.id',$request['from_station_id'])
            ->where('to_station.id',$request['to_station_id'])
            ->select('travel.*')
            ->first();


            $carTravel = new CarTravel();

            $car = Car::where('user_id',$request['user']->id)->first();
            $carType = CarType::find($car->car_type_id);
            $carTravel->from_station_id = $request['from_station_id'];
            $carTravel->to_station_id = $request['to_station_id'];
            $carTravel->departure_time = $departure_time;
            $carTravel->destination_time = $destination_time;
            $carTravel->travel_id = $travel ? $travel->id : null;

            $carTravel->car_id = $car->id;
            $carTravel->save();

            if ($request['stations']){
                foreach ($request['stations'] as $id) {
                    if (Station::where('id',$id)->exists()){
                        $travelStation = new CarTravelStation();
                        $travelStation->car_travel_id = $carTravel->id;
                        $travelStation->station_id = $id;
                        $travelStation->save();
                    }
                    else{
                        return response()->json('station_id error',400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
                    }
                }
            }


            for ($i = 1 ; $i <= $carType->count_places; $i++){
                $travelPlace = new CarTravelPlace();
                $travelPlace->car_travel_id = $carTravel->id;
                $travelPlace->driver_id = $request['user']->id;
                $travelPlace->number = $i;
                $travelPlace->from_station_id = $request['from_station_id'];
                $travelPlace->to_station_id = $request['to_station_id'];
                $travelPlace->save();

            }

            foreach ($request['place_price'] as $place_price) {

                CarTravelPlace::where('car_travel_id',$carTravel->id)
                    ->whereBetween('number',[$place_price['from'],$place_price['to']])
                    ->update([
                        'price' => $place_price['price']
                    ]);

            }
        };


        add($request,$request['departure_time'],$request['destination_time']);

        if ($request['times']){
            foreach ($request['times'] as $time) {
                add($request,$time['departure_time'],$time['destination_time']);
            }
        }


        return response()->json('success',200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }

    function travelDelete(Request $request){
        $rules = [
            "travel_id" => 'required|exists:car_travel,id',
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        CarTravel::where('id',$request['travel_id'])->delete();


        return response()->json('success',200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }

    //Driver,Passenger
    function travelMyList(Request $request){
        $rules = [
            'from_station_id' => 'exists:stations,id',
            'to_station_id' => 'exists:stations,id',
            'time' => 'date_format:Y-m-d',
            'page' => 'required|numeric|min:1',
            'limit' => 'required|numeric|min:5|max:50'
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $travels  = CarTravel::orderBy('car_travel.id','desc')
            ->select('car_travel.*');

        if ($request['user']->role == 'driver'){
            $travels = $travels->join('cars','car_travel.car_id','cars.id')
                ->where('cars.user_id',$request['user']->id);
        }else{
            $travels = $travels->join('car_travel_places','car_travel_places.car_travel_id','car_travel.id')
                ->where('car_travel_places.user_id',$request['user']->id);
        }


        if ($request['from_station_id']){
            $travels = $travels->where('from_station_id',$request['from_station_id']);
        }
        if ($request["to_station_id"]){
            $travels = $travels->where('to_station_id',$request['to_station_id']);
        }
        if ($request["time"]){
            $travels = $travels->whereRaw("DATE(car_travel.departure_time)",$request['time']);
        }

        $data = $this->paginate($travels,TravelResource::class,$request['page'],$request['limit']);

        return response()->json($data,200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }
    //Passenger
    function travelList(Request $request){
        $rules = [
            'from_station_id' => 'exists:stations,id',
            'to_station_id' => 'exists:stations,id',
            'date' => 'date_format:Y-m-d',
            'page' => 'required|numeric|min:1',
            'limit' => 'required|numeric|min:5|max:50',
            'baggage'=> "in:1,0",
            'tv'=> "in:1,0",
            'conditioner'=> "in:1,0",
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $travels  = CarTravel::join('cars','cars.id','car_travel.car_id')
            ->select('car_travel.*')
            ->whereRaw("car_travel.departure_time > CURRENT_TIMESTAMP()")
            ->orderBy('car_travel.id','desc');

        if ($request['from_station_id']){
            $travels = $travels->where('from_station_id',$request['from_station_id']);
        }
        if ($request["to_station_id"]){
            $travels = $travels->where('to_station_id',$request['to_station_id']);
//            $travels = $travels->join('travel','travel.id','car_travel.travel_id')
//                ->join('travel_stations','travel_stations.travel_id','travel.id')
//                ->where('travel_stations.station_id',$request['to_station_id']);
        }

        if ($request["date"]){
            $travels = $travels->whereRaw("DATE(car_travel.departure_time) = '$request->date'");
        }
        if ($request['baggage']){
            $travels = $travels->where('baggage',$request['baggage']);
        }
        if ($request['tv']){
            $travels = $travels->where('tv',$request['tv']);
        }
        if ($request['conditioner']){
            $travels = $travels->where('conditioner',$request['conditioner']);
        }

        $data = $this->paginate($travels,TravelResource::class,$request['page'],$request['limit']);

        return response()->json($data,200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
    }
    //Driver,Passenger
    function travelShow(Request $request){
        $rules = [
            'travel_id' => 'required|exists:car_travel,id',
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $data['travel'] = new TravelResource(CarTravel::find($request['travel_id']));
        $data['places'] = TravelPlaceResource::collection(CarTravelPlace::where('car_travel_id',$request['travel_id'])->get());

        return response()->json($data,200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }

    //Driver,Passenger
    function travelPlaces(Request $request){
        $rules = [
            'travel_id' => 'required|exists:car_travel,id',
        ];
        $messages = [
        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $data['travel'] = new TravelResource(CarTravel::find($request['travel_id']));
        $data['places'] = TravelPlaceResource::collection(CarTravelPlace::where('car_travel_id',$request['travel_id'])->get());

        return response()->json($data,200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }
    //Passenger
    function myTickets(Request $request){
        $places = CarTravelPlace::join('car_travel','car_travel_places.car_travel_id','car_travel.id')
            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')
            ->join('cars','car_travel.car_id','cars.id')
            ->join('car_types','cars.car_type_id','car_types.id')
            ->where('passenger_id',$request['user']->id)
            ->whereRaw("car_travel.destination_time > CURRENT_TIMESTAMP()")
                ->whereIn('status',['take'])
            ->select(
                'car_travel_places.id',
                'car_travel_places.number',
                'car_travel_places.price',
                'car_travel_places.status',
                'car_travel.departure_time',
                'car_travel.destination_time',
                'from_city.name as from_city',
                'from_station.name as from_station',
                'to_city.name as to_city',
                'to_station.name as to_station',
                'cars.state_number as car_state_number',
                'car_types.name as car_type',
                'car_types.count_places as car_type_count_places'
            )
            ->orderBy('car_travel_places.id','desc')
            ->get();



        return response()->json($places);
    }
    //Passenger
    function orderHistories(Request $request){
        $places = CarTravelPlaceOrder::join('car_travel','car_travel_place_orders.car_travel_id','car_travel.id')
            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')
            ->join('cars','car_travel.car_id','cars.id')
            ->join('car_types','cars.car_type_id','car_types.id')
            ->where('passenger_id',$request['user']->id)
            ->whereRaw("car_travel.destination_time < CURRENT_TIMESTAMP()")
            ->select(
                'car_travel_place_orders.id',
                'car_travel_place_orders.number',
                'car_travel_place_orders.price',
                'car_travel_place_orders.status',
                'car_travel.departure_time',
                'car_travel.destination_time',
                'from_city.name as from_city',
                'from_station.name as from_station',
                'to_city.name as to_city',
                'to_station.name as to_station',
                'cars.state_number as car_state_number',
                'car_types.name as car_type',
                'car_types.count_places as car_type_count_places',
                'car_travel_place_orders.created_at'
            )
            ->orderBy('created_at','desc')
            ->get();

        return response()->json($places);
    }

    //Passenger, Бронировать место
    function placeReservation(Request $request){

        $rules = [
            'travel_id' => 'required|exists:car_travel,id',
            'places' => 'required|array',
        ];

        $messages = [

        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
        $travel = CarTravel::find($request['travel_id']);

        $order = new CarTravelOrder();
        $order->car_travel_id = $travel->id;
        $order->passenger_id = $request['user']->id;
        $order->driver_id = Car::find($travel->car_id)->user_id;
        $order->from_station_id = $travel->from_station_id;
        $order->to_station_id = $travel->to_station_id;
        $order->status = 'in_process';
        $order->booking_time = Carbon::now();
        $order->save();


        foreach ($request['places'] as $place) {
            $place = CarTravelPlace::where('car_travel_id',$request['travel_id'])->where('number',$place)->first();
            if ($place){
                   if ($place == 'take'){
                       return response()->json("Место #$place уже забронирован",400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

                   }
                   if ($place == 'in_process'){
                       return response()->json("Место #$place уже забронирован",400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
                   }

                   $place->car_travel_order_id = $order->id;
                   $place->passenger_id = $request['user']->id;
                   $place->status = 'in_process';
                   $place->booking_time = Carbon::now();
                   $place->save();

                   $placeOrder = new CarTravelPlaceOrder();
                   $placeOrder->price = $place->price;
                   $placeOrder->car_travel_id = $place->car_travel_id;
                   $placeOrder->driver_id = $place->driver_id;
                   $placeOrder->number = $place->number;
                   $placeOrder->from_station_id = $place->from_station_id;
                   $placeOrder->to_station_id = $place->to_station_id;
                   $placeOrder->car_travel_order_id = $order->id;
                   $placeOrder->passenger_id = $request['user']->id;
                   $placeOrder->status = 'in_process';
                   $placeOrder->booking_time = Carbon::now();
                   $placeOrder->save();

                   $placeOrder->save();



            }else{
                return response()->json("Место не найдено",400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
            }
        }


        Firebase::sendMultiple(User::where('id',$request['user']->id)
            ->where('push',1)
            ->select('device_token')
            ->pluck('device_token')
            ->toArray(),[
            'title' => 'Saparline',
            'body' => "Оплатите через каспи",
            'type' => 'reservation',
            'user_id' => $request['user']->id,
        ]);

        return response()->json('success',200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }

    function placeCancel(Request $request){

        $rules = [
            'place_id' => 'required|exists:car_travel_places,id',
        ];
        $messages = [

        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $place = CarTravelPlace::find($request['place_id']);
        $place->status = 'cancel';
        $place->save();


        CarTravelPlaceOrder::where('passenger_id',$place->passenger_id)
            ->where('car_travel_id',$place->car_travel_id)
            ->where('number',$place->number)
            ->update([
                'status' => 'cancel'
            ]);




        return response()->json('success',200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }

    //Driver
    function travelPlaceEdit(Request $request){
        $rules = [
            'travel_place_id' => 'required|exists:car_travel_places,id',
            'status'=>'in:free,in_process,take',
            'passenger_id' => 'exists:users,id'
        ];
        $messages = [

        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $place = CarTravelPlace::find($request['travel_place_id']);

        if  ($request['passenger_id']){
            $place->passenger_id = $request['passenger_id'];
        }
        if  ($request['status']){
            $place->status = $request['status'];

            if ($request['status'] == 'take'){
                $place->added = 'driver';
            }
        }
        if  ($request['price']){
            $place->price = $request['price'];
        }
        if  ($request['number']){
            $place->number = $request['number'];
        }
        $place->save();

        return response()->json('success',200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }

    //Driver ,История поездок
    function travelHistories(Request $request){
        $travel = CarTravel::join('cars','car_travel.car_id','cars.id')
            ->where('cars.user_id',$request['user']->id)
            ->whereRaw("car_travel.destination_time < CURRENT_TIMESTAMP()")
            ->select('car_travel.*')
            ->get();


        return response()->json(TravelResource::collection($travel),200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }

    //Driver ,Грядущие поездки
    function travelUpcoming(Request $request){
        $travels = CarTravel::join('cars','car_travel.car_id','cars.id')
            ->where('cars.user_id',$request['user']->id)
            ->whereRaw("car_travel.destination_time >= CURRENT_TIMESTAMP()")
            ->orderBy('car_travel.id','desc')
            ->select('car_travel.*')
            ->limit(100)
            ->get();
        return response()->json(TravelResource::collection($travels),200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
    }

    //Driver , Мои пассажиры
    function travelMyPassengers(Request $request){
        $rules = [
        ];
        $messages = [

        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }



        $places = CarTravelPlaceOrder::join('car_travel','car_travel_place_orders.car_travel_id','car_travel.id')
            ->join('cars','car_travel.car_id','cars.id')
            ->where('cars.user_id',$request['user']->id)
            ->whereRaw("DATE(car_travel.destination_time) > NOW()")
            ->select('car_travel_place_orders.*')
            ->where('status','take')
            ->whereNotNull('passenger_id')
            ->orderBy('car_travel_place_orders.updated_at','desc')
            ->get();


        return response()->json(TravelPlaceResource::collection($places),200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);

    }

    function travelStations(Request $request){
        $rules = [
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id'
        ];
        $messages = [

        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }

        $travelStations = Travel::join('travel_stations','travel_stations.travel_id','travel.id')
            ->join('stations','stations.id','travel_stations.station_id')
            ->join('cities','cities.id','stations.city_id')
            ->where('travel.from_city_id',$request['from_city_id'])
            ->where('travel.to_city_id',$request['to_city_id'])
            ->select('cities.name as city',
                DB::raw('CONCAT(cities.name , " - " ,stations.name ) as stations'),
                'stations.id as station_id' ,
                'lat','lng'

            )
            ->get();



        return response()->json($travelStations,200,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
    }



}

