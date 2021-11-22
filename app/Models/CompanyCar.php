<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCar extends Model
{
    protected $table = 'company_cars';

    protected $fillable = [
        'company_id', 'car_id'
    ];
}
