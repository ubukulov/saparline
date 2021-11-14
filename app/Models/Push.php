<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Push extends Model
{
    protected $table = 'push';
    protected $hidden = ['created_at','updated_at'];
}
