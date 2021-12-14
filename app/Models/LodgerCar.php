<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LodgerCar extends Pivot
{
    protected $table = 'lodger_cars';

    protected $fillable = [
        'user_id', 'car_id'
    ];

    public static function exists($user_id, $car_id)
    {
        $result = LodgerCar::where(['user_id' => $user_id, 'car_id' => $car_id])->first();
        return ($result) ? true : false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public static function add($user_id, $car_id)
    {
        LodgerCar::create([
            'user_id' => $user_id, 'car_id' => $car_id
        ]);
    }
}
