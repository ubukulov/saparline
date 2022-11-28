<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarType;
use App\Models\DirectionPrice;
use App\Models\Travel;
use Illuminate\Http\Request;

class DirectionPriceController extends Controller
{
    public function index()
    {
        $direction_prices = DirectionPrice::all();
        return view('admin.direction_price.index', compact('direction_prices'));
    }

    public function create()
    {
        $travels = Travel::all();
        $car_types = CarType::all();
        return view('admin.direction_price.create', compact('travels', 'car_types'));
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token', 'data']);
        foreach($request->input('data') as $datum) {
            if($datum['from'] == $datum['to']) {
                $data['price'] = $datum['price'];
                $data['number'] = $datum['from'];
                DirectionPrice::create($data);
            } elseif($datum['from'] < $datum['to']) {
                $int = (int) $datum['from'];
                $doInt = (int) $datum['to'];
                $data['price'] = $datum['price'];
                for($i=$int; $i<=$doInt; $i++) {
                    $data['number'] = $i;
                    DirectionPrice::create($data);
                }
            }
        }

        return redirect()->route('admin.direction.index');
    }

    public function edit($id)
    {
        $direction_price = DirectionPrice::findOrFail($id);
        $travels = Travel::all();
        $car_types = CarType::all();
        return view('admin.direction_price.edit', compact('direction_price', 'travels', 'car_types'));
    }

    public function update(Request $request, $id)
    {
        $direction_price = DirectionPrice::findOrFail($id);
        $price = $request->input('price');
        $direction_price->price = $price;
        $direction_price->save();
        return redirect()->route('admin.direction.index');
    }
}
