<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
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
        return view('admin.car.edit', compact('car', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);
        $car->update($request->all());

        if (!CompanyCar::exists($request->input('company_id'), $car->id)) {
            CompanyCar::create([
                'company_id' => $request->input('company_id'), 'car_id' => $car->id
            ]);
        }
        return redirect()->route('admin.car.index');
    }
}
