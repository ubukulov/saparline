<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'title', 'address', 'phone', 'email', 'password', 'bin'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function cars()
    {
        return $this->hasMany(CompanyCar::class);
    }
}
