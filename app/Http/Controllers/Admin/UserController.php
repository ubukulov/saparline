<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Models\Car;
use App\Models\CarTravel;
use App\Models\CarTravelOrder;
use App\Models\CarTravelPlace;
use App\Models\CarTravelPlaceOrder;
use App\Models\CommentDislike;
use App\Models\CommentLike;
use App\Models\Favorite;
use App\Models\Post;
use App\Models\Push;
use App\Models\Subscription;
use App\Models\User;
use App\Packages\Firebase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller

{

    public function drivers(Request $request)
    {

        $user = User::join('cars','cars.user_id','users.id')
            ->join('car_types','car_types.id','cars.car_type_id')
            ->where('confirmation','confirm')
            ->orderBy('users.id','desc')
            ->select('users.*','car_types.name as car_type','car_types.count_places','cars.state_number');
        if ($request['search']) {
            $user = $user->where(function ($query) use ($request) {
                $query->where('users.phone', 'LIKE', "%$request->search%")
                    ->orWhere('users.name', 'LIKE', "%$request->search%");
            });
        }
        $data['users']= $user->paginate(50);
        $data['search']= $request['search'];
        return view('admin.user.drivers', $data);
    }
    public function passengers(Request $request)
    {

        $user = User::where('role','passenger')->orderBy('id','desc');
        if ($request['search']) {
            $user = $user->where(function ($query) use ($request) {
                $query->where('phone', 'LIKE', "%$request->search%")
                    ->orWhere('name', 'LIKE', "%$request->search%");
            });
        }
        $data['users']= $user->paginate(50);
        $data['search']= $request['search'];
        return view('admin.user.passengers', $data);
    }

    public function driver($id)
    {
        $data['user'] = User::findOrFail($id);
        $data['car'] = Car::where('user_id',$id)->first();
        $data['travels'] = CarTravel::join('cars','cars.id','car_travel.car_id')
            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')

            ->where('cars.user_id',$id)
            ->orderBy('car_travel.id','desc')
            ->select('car_travel.id','departure_time','destination_time',
                'from_city.name as from_city','from_station.name as from_station',
                'to_city.name as to_city','to_station.name as to_station'
            )
            ->get();

        $data['places'] = CarTravelPlace::join('car_travel','car_travel.id','car_travel_places.car_travel_id')
            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')
            ->join('users as driver','driver.id','car_travel_places.driver_id')
            ->join('users as passenger','passenger.id','car_travel_places.passenger_id')

            ->where('car_travel_places.added','admin')
            ->where('car_travel_places.driver_id',$id)

            ->select(
                'car_travel_places.id',
                'car_travel_id',
                'number',
                'price',
                'departure_time',
                'destination_time',
                'from_city.name as from_city',
                'from_station.name as from_station',
                'to_city.name as to_city',
                'to_station.name as to_station',
                'driver.name as driver_name',
                'driver.phone as driver_phone'
            )
            ->orderBy('car_travel_places.id','desc')
            ->get();

        return response()->view('admin.user.driver',$data);
    }
    public function travelPlaces($id)
    {

        $data['places'] = CarTravelPlace::join('car_travel','car_travel.id','car_travel_places.car_travel_id')
            ->leftJoin('users as passenger','passenger.id','car_travel_places.passenger_id')

            ->where('car_travel.id',$id)

            ->select(
                'number',
                'price',
                'passenger.name as passenger_name',
                'passenger.phone as passenger_phone',
                'car_travel_places.added'
            )
            ->orderBy('car_travel_places.number')
            ->get();

        return response()->view('admin.user.travel_places',$data);
    }
    public function passenger($id)
    {
        $data['travels'] = CarTravel::join('car_travel_places','car_travel.id','car_travel_places.car_travel_id')

            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')

            ->where('car_travel_places.passenger_id',$id)
            ->orderBy('car_travel.id','desc')
            ->select('car_travel.id','departure_time','destination_time','price','number',
                'from_city.name as from_city','from_station.name as from_station',
                'to_city.name as to_city','to_station.name as to_station'
            )
            ->paginate(50);
        return response()->view('admin.user.passenger',$data);
    }


    public function confirmation()
    {

        $data['drivers'] = Car::where(['is_confirmed' => 0])
            ->with('user')
            ->join('car_types','car_types.id','cars.car_type_id')
            ->orderBy('cars.id','desc')
            ->select('cars.*', 'car_types.name as car_type','car_types.count_places','cars.state_number')
            ->paginate(50);

        return response()->view('admin.user.confirmation',$data);

        /*$data['drivers'] = User::where('role','driver')
            ->where('confirmation','waiting')
            ->join('cars','cars.user_id','users.id')
            ->join('car_types','car_types.id','cars.car_type_id')
            ->orderBy('users.id','desc')
            ->select('users.*','car_types.name as car_type','car_types.count_places','cars.state_number')
            ->paginate(50);
        return response()->view('admin.user.confirmation',$data);*/

    }
    public function confirmationConfirm($car_id){

        //User::where('id',$id)->update(['confirmation' => 'confirm']);
        $car = Car::findOrFail($car_id);
        $car->is_confirmed = 1;
        $car->save();

        $user = $car->user;

        if ($user->confirmation == 'waiting') {
            $user->confirmation = 'confirm';
            $user->save();
        }

        Firebase::sendMultiple(User::where('id',$user->id)
            ->where('push',1)
            ->select('device_token')
            ->pluck('device_token')
            ->toArray(),[
            'title' => 'Saparline',
            'body' => "ваши данные подтверждены",
            'type' => 'driver_confirmation',
            'user_id' => $user->id,
        ]);


        return redirect()->back();

    }
    public function confirmationReject($id){

        User::where('id',$id)->update(['confirmation' => 'reject']);

        Firebase::sendMultiple(User::where('id',$id)->select('device_token')->pluck('device_token')->toArray(),[
            'title' => 'Saparline',
            'body' => "ваши данные отклонены",
            'type' => 'driver_reject',
            'user_id' => $id,
        ]);


        return redirect()->back();

    }


    public function edit($id, Request $request)
    {
        $data['user'] = User::findOrFail($id);
        $data['car'] = Car::whereUserId($id)->first();

        return view('admin.user.edit',$data);

    }

    public function update($id,Request $request)
    {
        $rules = [
            'name'=> 'required',
            'phone'=> 'required',
        ];
        $messages = [

        ];
        $validator = $this->validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $user = User::findOrFail($id);
        if ($request['name']){
            $user->name = $request['name'];
        }
        if ($request['phone']){
            if (User::where('id','<>',$user->id)->where('phone',$request['phone'])->exists()){
                return back()->withErrors('Телефон номер занять');
            }
            $user->phone = $request['phone'];
        }
        if ($request['avatar']){
            $this->deleteFile($user->avatar);
            $user->avatar = $this->uploadFile($request['avatar'],'avatars/user_'.$user->id);
        }
        if ( $request['password_new']){
//            if (!Hash::check($request['password_old'],$user->password)){
//                return back()->withErrors('Неверный пароль');
//            }
            $user->password = bcrypt($request['password_new']);
        }
        $user->save();

        if ($user->role == 'driver'){
            return redirect()->route('admin.user.drivers');
        }else{
            return redirect()->route('admin.user.passengers');
        }
    }

    public function destroy($id)
    {
        /*$l = User::findOrFail($id);
        $l->delete();
        return redirect()->back();*/
        $user = User::findOrFail($id);

    }


    public function travels(Request $request)
    {

        $travels = CarTravel::join('cars','cars.id','car_travel.car_id')
            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')
            ->join('users','users.id','cars.user_id')

            ->orderBy('car_travel.id','desc')
            ->select('car_travel.id','departure_time','destination_time',
                'from_city.name as from_city','from_station.name as from_station',
                'to_city.name as to_city','to_station.name as to_station',
                'users.name','users.phone','cars.state_number'
            );




        if ($request['search']) {
            $user = $travels->where(function ($query) use ($request) {
                $query->where('car_travel.id', "$request->search")
                    ->orWhere('cars.state_number', 'LIKE', "%$request->search%");
            });
        }
        $data['travels']= $travels->paginate(50);
        $data['search']= $request['search'];

        return response()->view('admin.car_travel.list',$data);
    }
    public function orders(Request $request){

        $data['travels'] = CarTravelOrder::join('car_travel','car_travel_orders.car_travel_id','car_travel.id')
            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')
            ->join('cars','car_travel.car_id','cars.id')
            ->join('car_types','cars.car_type_id','car_types.id')
            ->join('users as driver','driver.id','cars.user_id')
            ->join('users as passenger','passenger.id','car_travel_orders.passenger_id')
            ->where('status',"in_process")
            ->select(
                'car_travel_orders.id',
                'car_travel_orders.status',
                'car_travel.departure_time',
                'car_travel.destination_time',
                'from_city.name as from_city',
                'from_station.name as from_station',
                'to_city.name as to_city',
                'to_station.name as to_station',
                'cars.state_number as car_state_number',
                'car_types.name as car_type',
                'car_types.id as car_type_id',
                'driver.name as driver_name',
                'driver.phone as driver_phone',
                'passenger.name as passenger_name',
                'passenger.phone as passenger_phone',
                'booking_time'
            )
            ->orderBy('booking_time','desc')
            ->paginate(15);



        return view('admin.car_travel.orders',$data);
    }
    public function cancelOrders(Request $request){

        $data['travels'] = CarTravelPlaceOrder::join('car_travel','car_travel_place_orders.car_travel_id','car_travel.id')
            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')
            ->join('cars','car_travel.car_id','cars.id')
            ->join('car_types','cars.car_type_id','car_types.id')
            ->join('users as driver','driver.id','cars.user_id')
            ->leftJoin('users as passenger','passenger.id','car_travel_place_orders.passenger_id')
            ->where('status',"cancel")
			->whereDate('car_travel.departure_time', '>=', date('Y-m-d'))
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
                'driver.name as driver_name',
                'driver.phone as driver_phone',
                'passenger.name as passenger_name',
                'passenger.phone as passenger_phone',
                'car_travel_place_orders.reason_for_return',
            )
            ->orderBy('booking_time','desc')
            ->paginate(15);



        return view('admin.car_travel.cancel_orders',$data);
    }
    public function orderTake($id){

        $order = CarTravelOrder::find($id);
        $order->status = 'take';
        $order->added = 'admin';
        $order->save();





        CarTravelPlace::where('car_travel_order_id',$id)->update([
            'status' => 'take',
            'added' => 'admin'
        ]);
        CarTravelPlaceOrder::where('car_travel_order_id',$id)->update([
            'status' => 'take',
            'added' => 'admin'
        ]);


        Firebase::sendMultiple(User::whereIn('id',[$order->driver_id,$order->passenger_id])->select('device_token')->pluck('device_token')->toArray('device_token'),[
            'title' => 'Saparline',
            'body' => "Место забронировано",
            'type' => 'place_take',
            'travel_place_id' => $id,
        ]);






        return redirect()->back();
    }
    public function orderReject($id){


        $order = CarTravelOrder::find($id);
        $order->delete();

        CarTravelPlace::where('car_travel_order_id',$id)->update([
            'status' => 'free',
            'passenger_id' => null,
            'car_travel_order_id' => null,
        ]);
        CarTravelPlaceOrder::where('car_travel_order_id',$id)->update([
            'status' => 'take',
            'added' => 'admin'
        ]);

        return redirect()->back();
    }
    public function orderCancel($id){
        $order = CarTravelPlaceOrder::findOrFail($id);

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

        $order->status = 'cancel';
        $order->save();

		/*
        $order = CarTravelPlace::findOrFail($id);

        $order->status = 'free';
        $order->passenger_id = null;
        $order->car_travel_order_id = null;
        $order->save();*/

		$car_travel_place = CarTravelPlace::where(['car_travel_id' => $order->car_travel_id, 'car_travel_order_id' => $order->car_travel_order_id, 'number' => $order->number])->first();
		if ($car_travel_place) {
			$car_travel_place->status = 'free';
			$car_travel_place->passenger_id = null;
			$car_travel_place->car_travel_order_id = null;
			$car_travel_place->save();

			CarTravelPlaceOrder::destroy($id);
		} else {
			abort(404);
		}


        return redirect()->back();
    }

    public function calculatePlace(Request $request){
        $data['places'] = CarTravelPlace::join('car_travel','car_travel.id','car_travel_places.car_travel_id')
            ->join('stations as from_station','from_station.id','car_travel.from_station_id')
            ->join('stations as to_station','to_station.id','car_travel.to_station_id')
            ->join('cities as from_city','from_city.id','from_station.city_id')
            ->join('cities as to_city','to_city.id','to_station.city_id')
            ->join('users as driver','driver.id','car_travel_places.driver_id')
            ->join('users as passenger','passenger.id','car_travel_places.passenger_id')

            ->where('car_travel_places.added','admin')

            ->select(
                'car_travel_places.id',
                'car_travel_id',
                'number',
                'price',
                'departure_time',
                'destination_time',
                'from_city.name as from_city',
                'from_station.name as from_station',
                'to_city.name as to_city',
                'to_station.name as to_station',
                'driver.name as driver_name',
                'driver.phone as driver_phone'
            )
            ->paginate(20);

        return response()->view('admin.calculate',$data);
    }

    public function confirmationLodger()
    {
        $lodgers = User::where(['role' => 'lodger', 'confirmation' => 'waiting'])
            ->with('company')
            ->orderBy('users.id','desc')
            ->paginate(50);
        return response()->view('admin.user.confirmationLodger', compact('lodgers'));
    }

    public function confirmLodger($id)
    {
        $user = User::findOrFail($id);
        $user->confirmation = 'confirm';
        $user->save();

        Firebase::sendMultiple(User::where('id',$user->id)
            ->where('push',1)
            ->select('device_token')
            ->pluck('device_token')
            ->toArray(),[
            'title' => 'Saparline',
            'body' => "ваши данные подтверждены",
            'type' => 'driver_confirmation',
            'user_id' => $user->id,
        ]);


        return redirect()->back();
    }

    public function rejectLodger($id)
    {
        $user = User::findOrFail($id);
        $user->confirmation = 'reject';
        $user->save();

        Firebase::sendMultiple(User::where('id',$id)->select('device_token')->pluck('device_token')->toArray(),[
            'title' => 'Saparline',
            'body' => "ваши данные отклонены",
            'type' => 'driver_reject',
            'user_id' => $id,
        ]);


        return redirect()->back();
    }

    public function lodgers()
    {
        $lodgers = User::where(['role' => 'lodger', 'confirmation' => 'confirm'])
            ->orderBy('users.id','desc')
            ->paginate(50);
        return response()->view('admin.user.lodgers', compact('lodgers'));
    }
}
