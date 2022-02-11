<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestPlace extends Model
{
    protected $table = 'resting_places';

    protected $fillable = [
        'city_id', 'title', 'description', 'active'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
