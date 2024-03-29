<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\UserTravel;
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
            return response()->json('Запись успешно принята!', 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getListMyNotice(Request $request)
    {
        $user = $request['user'];
        $my_travel_list_notices = UserTravel::where(['user_id' => $user->id])
            ->with('from_city', 'to_city')
            ->get();
        return response()->json($my_travel_list_notices);
    }

    public function deleteNotification($id)
    {
        $user_travel = UserTravel::findOrFail($id);
        if($user_travel) {
            UserTravel::destroy($id);
            return response('success', 200);
        } else {
            return response('already deleted', 403);
        }
    }
}
