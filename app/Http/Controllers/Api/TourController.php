<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeetPlace;
use App\Models\RestPlace;
use Illuminate\Http\Request;

class TourController extends Controller
{
    protected $imgArrays;

    public function getToursForToday()
    {

    }

    public function getInfo($tour_id)
    {

    }

    public function bookingPlace(Request $request)
    {

    }

    public function searchingTour(Request $request)
    {

    }

    public function buyTour(Request $request)
    {

    }

    public function getRestingPlaces($city_id)
    {
        $resting_places = RestPlace::where(['city_id' => $city_id])->get();
        return response()->json($resting_places);
    }

    public function getMeetingPlaces($city_id)
    {
        $meeting_places = MeetPlace::where(['city_id' => $city_id])->get();
        return response()->json($meeting_places);
    }

    public function uploadPreview(Request $request)
    {
        foreach($request['file'] as $img) {
            $this->uploadFile($img, 'tours');
        }

        return response()->json('success');
    }

    public function getUploadedFiles()
    {
        return response()->json();
    }
}
