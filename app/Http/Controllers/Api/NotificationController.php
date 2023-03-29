<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\UserTravel;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get(Request $request)
    {
        $user = $request['user'];
        $notifications = $user->notifications;
        return response()->json($notifications, 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function signForNotice(Request $request)
    {
        $data = $request->all();
        $user_travel = UserTravel::where(['user_id' => $data['user_id'], 'from_city_id' => $data['from_city_id'], 'to_city_id' => $data['to_city_id']])->first();
        if($user_travel) {
            return response()->json("Запись с такими параметрами уже имеется", 406, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        } else {
            UserTravel::create($data);
            return response()->json('Запись успешно принять!', 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getListMyNotice(Request $request)
    {
        $user = $request['user'];
        $my_travel_list_notices = UserTravel::where(['user_id' => $user->id])->get();
        return response()->json($my_travel_list_notices);
    }
}
