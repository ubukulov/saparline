<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'title', 'address', 'phone', 'email', 'password', 'bin', 'type_id'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function cars()
    {
        //return $this->hasMany(CompanyCar::class);
        return $this->belongsToMany(Car::class, 'company_cars');
    }
}
