<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashboxResource;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::guard('cashbox')->attempt(['username' => $request->input('username'), 'password' => $request->input('password')])){
            return response()->json(['user' => CashboxResource::collection(Auth::guard('cashbox')->user())], 200);
        } else {
            return response()->json('Не найден пользователь', 404);
        }
    }

    public function logout()
    {
        Auth::guard('cashbox')->logout();
        return response()->json('Успешно.', 200);
    }
}
