<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashier;
use App\Models\City;
use App\Models\Company;
use App\Models\Station;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        $cashiers = Cashier::orderBy('id', 'DESC')
            ->with('city', 'station')
            ->where(['type_id' => 1])
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
}
