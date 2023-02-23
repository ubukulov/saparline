<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarMark;
use App\Models\Company;
use App\Models\CompanyCar;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::orderBy('id', 'DESC')
            ->with('car_type')
            ->paginate(20);
        return view('admin.car.index', compact('cars'));
    }

    public function edit($id)
    {
        $car = Car::findOrFail($id);
        $companies = Company::all();
        $marks = CarMark::all();
        return view('admin.car.edit', compact('car', 'companies', 'marks'));
    }

    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);
        $car->update($request->all());

		$company_car = CompanyCar::where(['car_id' => $car->id])->first();
		if ($company_car) {
			$company_car->company_id = $request->input('company_id');
			$company_car->save();
		} else {
			CompanyCar::create([
                'company_id' => $request->input('company_id'), 'car_id' => $car->id
            ]);
		}

        return redirect()->route('admin.car.index');
    }
}
