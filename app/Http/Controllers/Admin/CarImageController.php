<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarImageController extends Controller
{
    public function index()
    {
        $cars = Car::where(['is_confirmed' => 3])
                ->with('car_type', 'user')
                ->get();
        return view('admin.car.change-images-list', compact('cars'));
    }

    public function show($car_id)
    {
        $car = Car::findOrFail($car_id);
        $dataImages = json_decode($car->new_images);
        return view('admin.car.show-image', compact('car', 'dataImages'));
    }

    public function approveImages($car_id)
    {
        $car = Car::findOrFail($car_id);
        if($car->is_confirmed == 3 && !is_null($car->new_images)) {
            $dataImages = json_decode($car->new_images);
            $car->image = (is_null($dataImages->image)) ? null : $dataImages->image;
            $car->image1 = (is_null($dataImages->image1)) ? null : $dataImages->image1;
            $car->image2 = (is_null($dataImages->image2)) ? null : $dataImages->image2;
            $car->avatar = (is_null($dataImages->avatar)) ? null : $dataImages->avatar;

            $car->passport_image = (is_null($dataImages->passport_image)) ? null : $dataImages->passport_image;
            $car->passport_image_back = (is_null($dataImages->passport_image_back)) ? null : $dataImages->passport_image_back;
            $car->identify_image = (is_null($dataImages->identify_image)) ? null : $dataImages->identify_image;
            $car->identify_image_back = (is_null($dataImages->identify_image_back)) ? null : $dataImages->identify_image_back;

            $car->new_images = null;
            $car->is_confirmed = 1;
            $car->save();
        }

        return redirect()->route('admin.car.change-images');
    }

    public function rejectImages($car_id)
    {
        $car = Car::findOrFail($car_id);
        if($car->is_confirmed == 3 && !is_null($car->new_images)) {
            $car->new_images = null;
            $car->is_confirmed = 2;
            $car->save();
        }

        return redirect()->route('admin.car.change-images');
    }
}
