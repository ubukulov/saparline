<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  \Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\ChatUser;



Route::prefix('v1')->namespace('Api')->group(function () {
	# Cashier
	Route::group(['prefix' => 'cashier'], function(){
		# Создать поездки
		Route::post('login', 'CashierController@login');
		Route::post('register', 'CashierController@register');
		Route::get('companies/list', 'CashierController@getCompanies');
		Route::get('companies/{id}/get-cars-list', 'CashierController@getCompanyCarsList');
		Route::get('cities/list', 'CashierController@getCities');
		Route::get('cities/{id}/get-stations', 'CashierController@getCityStationsList');
		Route::post('create-trip', 'CashierController@createTripByCashier');
		Route::get('get-travels-upcoming', 'CashierController@travelUpcoming');
		Route::get('car/{id}/get-info', 'CashierController@getCarInfo');

		# Продажа билетов
		Route::get('tickets/get-tickets-for-today', 'CashierController@getTicketsForToday');
		Route::get('tickets/get-tickets-for-next-days', 'CashierController@getTicketsForNextDays');
		Route::get('car-travel/{id}/get-all-places-for-route', 'CashierController@getAllPlacesForRoute');

		Route::post('car-travel/{id}/selling', 'CashierController@ticketSelling');

		Route::get('tickets/get-sold-tickets-for-today', 'CashierController@getSoldTicketsForToday');
		Route::get('tickets/{filter_id}/get-tickets-by-filter', 'CashierController@getTicketsByFilter');

		Route::post('tickets/return-sold-tickets', 'CashierController@returnSoldTickets');
		Route::get('tickets/get-return-tickets', 'CashierController@getReturnTickets');

		# Плановые поездки
		Route::post('intercity/get-travels-by-filter', 'CashierController@getTravelsByFilter');
		Route::get('car-travel/{id}/get-information-about-car-travel', 'CashierController@getInformationAboutCarTravel');
		Route::get('car-travel/{id}/get-sold-tickets-for-current-route', 'CashierController@getSoldTicketsForCurrentRoute');
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
    Route::get('travel-stations','UserController@travelStations');
    Route::get('companies', 'CashierController@getCompanies');

    Route::post('checking-confirmation-for-change-role', 'UserController@checkingConfirmationForChangeRole');


    Route::middleware(['apiCheck'])->group(function () {
        Route::post('profile/logout','UserController@logout');
        Route::post('profile/confirmation','UserController@confirmation');
        Route::post('profile/edit','UserController@edit');
        Route::post('profile/role/passenger','UserController@rolePassenger');
        Route::post('profile/role/driver','UserController@roleDriver');
        Route::post('profile/role/lodger','UserController@roleLodger');
        Route::get('profile/carTravels','UserController@carTravels');
        Route::get('profile/cars','UserController@cars');

        Route::post('profile/addCar','UserController@addCar');

        Route::post('travel/add','UserController@travelAdd');
        Route::post('travel/delete','UserController@travelDelete');
        Route::get('travel/my-list','UserController@travelMyList');
        Route::get('travel/list','UserController@travelList');
        Route::get('travel/histories','UserController@travelHistories');

        Route::get('travel/show','UserController@travelShow');

        Route::get('my-tickets-new','UserController@myTickets');
        Route::get('my-tickets-groupped','UserController@myTicketsGroupped');
        Route::get('order-histories','UserController@orderHistories');

        Route::post('travel/place/reservation','UserController@placeReservation');
        Route::post('travel/place/reservation2','UserController@placeReservation2');
        Route::post('travel/place/cancel','UserController@placeCancel');
        Route::post('travel/place/edit','UserController@travelPlaceEdit');

        Route::get('travel/upcoming','UserController@travelUpcoming');
        Route::get('travel/my-passengers','UserController@travelMyPassengers');
        Route::get('travel/my-passengers-groupped','UserController@travelMyPassengersGroupped');

        Route::get('feedback/list','UserController@feedbackList');
        Route::post('toFeedback','UserController@toFeedback');

        Route::get('call', 'UserController@call');
        Route::get('order/take/{id}', 'UserController@orderTake');

        # Lodgers Api
        Route::group(['prefix' => 'lodger'], function(){
            Route::get('company/{company_id}/get-cars-list', 'LodgerController@getCarsList');
            Route::post('fix-selected-cars-for-me', 'LodgerController@fixSelectedCarsForMe');
            Route::get('{user_id}/get-selected-cars-list', 'LodgerController@getSelectedCarsList');
            Route::get('car-travel/{car_travel_id}/get-all-places-for-route', 'LodgerController@getAllPlacesForRoute');
            Route::post('car-travel/{car_travel_id}/selling', 'LodgerController@ticketSelling');
            Route::get('car-travel/{car_travel_id}/get-all-sold-places-for-route', 'LodgerController@getAllSoldPlacesForRoute');
            Route::post('car-travel/{car_travel_id}/multiple-selling', 'LodgerController@ticketMultipleSelling');
        });
    });
});
