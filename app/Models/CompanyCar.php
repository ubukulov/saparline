<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyCar extends Pivot
{
    protected $table = 'company_cars';

    protected $fillable = [
        'company_id', 'car_id'
    ];

    public static function exists($company_id, $car_id)
    {
        $result = CompanyCar::where(['company_id' => $company_id, 'car_id' => $car_id])->first();
        return ($result) ? true : false;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
