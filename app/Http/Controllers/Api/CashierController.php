<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashierResource;
use App\Models\Cashier;
use App\Models\CompanyCar;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;


class CashierController extends Controller {

	public function login(Request $request)
	{
		$rules = [
            'username'=> 'required',
            'password'=> 'required',
        ];

        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(),400,['charset'=>'utf-8'],JSON_UNESCAPED_UNICODE);
        }
		$username = $request->input('username');
		$password = $request->input('password');
		$cashier = Cashier::where(['username' => $username])->first();

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

	public function getCompanies()
	{
		return response()->json(Company::all());
	}

	public function getCompanyCarsList($company_id)
    {
        $company_cars = CompanyCar::where(['company_id' => $company_id])
			->selectRaw('cars.id, cars.state_number as number')
            ->join('cars', 'cars.id', '=', 'company_cars.car_id')
            ->get();
        return response()->json($company_cars);
    }
}
