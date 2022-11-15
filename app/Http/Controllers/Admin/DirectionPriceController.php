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

    }
}
