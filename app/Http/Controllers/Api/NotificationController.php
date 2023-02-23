<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get(Request $request)
    {
        $user = $request['user'];
        $notifications = $user->notifications;
        return response()->json($notifications, 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
