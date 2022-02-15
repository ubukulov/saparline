<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourImage extends Model
{
    protected $table = 'tours_images';

    protected $fillable = [
        'tour_id', 'image'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
