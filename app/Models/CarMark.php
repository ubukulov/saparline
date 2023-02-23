<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarMark extends Model
{
    protected $table = 'car_marks';

    protected $fillable = [
        'name'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
