<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarMark;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getCarMarks()
    {
        $marks = CarMark::all();
        return response()->json($marks, 200, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
