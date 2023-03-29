<?php

namespace App;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserTravel extends Model
{
    protected $table = 'user_travels';

    protected $fillable = [
        'user_id', 'from_city_id', 'to_city_id', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function from_city()
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function to_city()
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }
}
