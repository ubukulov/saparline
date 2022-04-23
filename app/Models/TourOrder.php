<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourOrder extends Model
{
    protected $table = 'tour_orders';

    protected $fillable = [
        'tour_id', 'car_id', 'passenger_id', 'agent_id', 'number', 'status', 'price', 'first_name', 'phone', 'iin', 'reason_for_return',
        'booking_time'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function passenger()
    {
        return $this->belongsTo(User::class);
    }
}
