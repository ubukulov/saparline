<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $hidden = ['created_at','updated_at'];

    protected $fillable = [
        'state_number'
    ];

    public function car_type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function getCompanyName()
    {
        $company_car = CompanyCar::where(['car_id' => $this->id])->first();
        if ($company_car) {
            return $company_car->company->title;
        }

        return 'Не определено';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
