<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashboxResource;
use App\Models\Cashier;
use Illuminate\Http\Request;
use Auth;

class CashierController extends Controller {
	
	public function login(Request $request)
	{
		dd($request);
		$rules = [
            'username'=> 'required',
            'password'=> 'required',
        ];
		
        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
		
		if(Auth::guard('cashbox')->attempt(['username' => $request->input('username'), 'password' => $request->input('password')])) {
			return response()->json(['user' => new CashboxResource(Auth::guard('cashbox')->user)]);
		}
		
		return response()->json('Неверные данные', 400);
	}
}