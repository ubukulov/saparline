<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectionPrice extends Model
{
    protected $table = 'direction_prices';

    protected $fillable = [
        'travel_id', 'car_type_id', 'number', 'price'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function travel()
    {
        return $this->belongsTo(Travel::class);
    }

    public function car_type()
    {
        return $this->belongsTo(CarType::class, 'car_type_id', 'id');
    }
}
