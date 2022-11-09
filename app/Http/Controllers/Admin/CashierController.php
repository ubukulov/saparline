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
		$userIds = [6461
,6485
,6574
,6576
,6666
,6668
,6701
,6704
,6718
,6719
,6720
,6721
,6722
,6723
,6724
,6726
,6730
,6731
,6732
,6734
,6736
,6737
,6740
,6742
,6744
,6746
,6747
,6749
,6753
,6754
,6755
,6756
,6759
,6760
,6761
,6763
,6765
,6766
,6769
,6770
,6771
,6773
,6774
,6775
,6776
,6777
,6782
,6783
,6784
,6785
,6786];
        //$users = User::whereIn('id', $userIds)->whereRole('passenger')->get();
        //$ids = [5410];
        /*foreach($users as $user) {
            $ids[] = $user->id;
        }*/

		//$ids = [5410,6794];
		$ids = [5410];
        $message = "Уважаемый пользователь, мы обнаружили ошибку при вашей регистрации. Просим извинения за доставленные неудобства. На данный момент все неполадки устранены. Выполните вход через логин и пароль. Воспользуйтесь функцией «Восстановить», если не помните свой пароль.";
        Firebase::sendMultiple(User::whereIn('id', $ids)
            ->select('device_token')->pluck('device_token')->toArray('device_token'), [
            'title' => 'Saparline',
            'body' => $message,
            'type' => 'place_take',
        ]);
    }
}
