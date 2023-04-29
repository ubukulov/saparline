<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashier;
use App\Models\City;
use App\Models\Company;
use App\Models\Station;
use App\Models\User;
use App\Packages\Firebase;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        $cashiers = Cashier::orderBy('id', 'DESC')
            ->with('city', 'station')
            //->where(['type_id' => 1])
            ->get();
        return view('admin.cashier.index', compact('cashiers'));
    }

    public function edit($cashier_id)
    {
        $cashier = Cashier::findOrFail($cashier_id);
        $cities = City::all();
        $stations = Station::all();
        return view('admin.cashier.edit', compact('cashier', 'cities', 'stations'));
    }

    public function update(Request $request, $id)
    {
        $cashier = Cashier::findOrFail($id);
        $data = $request->all();

        if ($cashier->active == 0 && $request->input('active') == 1) {
            $company = Company::where(['email' => $request->input('email')])->first();
            if (!$company) {
                $company = Company::create([
                    'title' => $request->input('company_name'), 'phone' => $request->input('phone'),
                    'email' => $request->input('email')
                ]);
                $data['company_id'] = $company->id;
            }
            $data['company_id'] = $company->id;
        }

        $cashier->update($data);

        return redirect()->route('admin.cashier.index');
    }

    public function firebase()
    {
		$userIds = [3971];
        //$users = User::whereIn('id', $userIds)->whereRole('passenger')->get();
        //$ids = [5410];
        /*foreach($users as $user) {
            $ids[] = $user->id;
        }*/

		//$ids = [5410,6794];
		$ids = [3971, 226];
        $message = "Уважаемый пользователь, мы обнаружили ошибку при вашей регистрации. Просим извинения за доставленные неудобства. На данный момент все неполадки устранены. Выполните вход через логин и пароль. Воспользуйтесь функцией «Восстановить», если не помните свой пароль.";
        Firebase::sendMultiple(User::whereIn('id', $ids)
            ->select('device_token')->pluck('device_token')->toArray('device_token'), [
            'title' => 'Saparline',
            'body' => $message,
            'type' => 'place_take',
        ]);
    }
}
