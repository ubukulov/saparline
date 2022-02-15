<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetPlace extends Model
{
    protected $table = 'meeting_place';

    protected $fillable = [
        'city_id', 'title', 'latitude', 'longitude'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
