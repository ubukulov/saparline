<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  \Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\ChatUser;



Route::prefix('v1')->namespace('Api')->group(function () {
	# Cashier
	Route::group(['prefix' => 'cashier'], function(){
		Route::post('login', 'CashierController@login');
		Route::get('companies/list', 'CashierController@getCompanies');
		Route::get('companies/{id}/get-cars-list', 'CashierController@getCompanyCarsList');
	});

    Route::post('profile/register','UserController@register');
    Route::post('profile/phone-confirmation','UserController@phoneConfirmation');
    Route::post('profile/login','UserController@login');
    Route::post('profile/loginByToken','UserController@loginByToken');
    Route::post('profile/password-reset/send','UserController@passwordResetSend');
    Route::post('profile/password-reset/check','UserController@passwordResetCheck');


    Route::get('user/getById','UserController@getById');
    Route::get('user/search','UserController@search');

    Route::get('setting','UserController@setting');

    Route::get('cities','UserController@cities');
    Route::get('stations','UserController@stations');
    Route::get('carTypes','UserController@carTypes');
    Route::get('travel-stations','UserController@travelStations');

    Route::middleware(['apiCheck'])->group(function () {
        Route::post('profile/logout','UserController@logout');
        Route::post('profile/confirmation','UserController@confirmation');
        Route::post('profile/edit','UserController@edit');
        Route::post('profile/role/passenger','UserController@rolePassenger');
        Route::post('profile/role/driver','UserController@roleDriver');

        Route::post('travel/add','UserController@travelAdd');
        Route::post('travel/delete','UserController@travelDelete');
        Route::get('travel/my-list','UserController@travelMyList');
        Route::get('travel/list','UserController@travelList');
        Route::get('travel/histories','UserController@travelHistories');

        Route::get('travel/show','UserController@travelShow');

        Route::get('my-tickets','UserController@myTickets');
        Route::get('order-histories','UserController@orderHistories');

        Route::post('travel/place/reservation','UserController@placeReservation');
        Route::post('travel/place/cancel','UserController@placeCancel');
        Route::post('travel/place/edit','UserController@travelPlaceEdit');

        Route::get('travel/histories','UserController@travelHistories');
        Route::get('travel/upcoming','UserController@travelUpcoming');
        Route::get('travel/my-passengers','UserController@travelMyPassengers');

    });
});
