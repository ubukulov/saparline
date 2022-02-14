<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::name('admin.')->namespace('Admin')->group(function () {
    Route::any('admin', 'MainController@login')->name("login");
    Route::group(['prefix' => 'admin', 'middleware' => 'adminCheck'], function () {
        Route::get('main', 'MainController@main')->name("main");
        Route::get('out', 'MainController@out')->name("out");
        Route::get('setting', 'MainController@setting')->name("setting");
        Route::get('calculatePlace', 'UserController@calculatePlace')->name("calculatePlace");


        Route::name('car_travel.')->prefix('car_travel')->group(function () {
            Route::get('/', 'UserController@travels')->name('list');
            Route::get('orders', 'UserController@orders')->name("orders");
            Route::get('/orders/{id}', 'UserController@orderDetail')->name('order.Detail');
            Route::get('order/take/{id}', 'UserController@orderTake')->name("orderTake");
            Route::get('order/reject/{id}', 'UserController@orderReject')->name("orderReject");
            Route::get('cancelOrders', 'UserController@cancelOrders')->name("cancelOrders");
            Route::get('order/cancel/{id}', 'UserController@orderCancel')->name("orderCancel");
        });
        Route::name('user.')->prefix('user')->group(function () {
            Route::get('drivers', 'UserController@drivers')->name('drivers');
            Route::get('passengers', 'UserController@passengers')->name('passengers');
            Route::get('driver/{id}', 'UserController@driver')->name('driver');
            Route::get('driver/travelPlaces/{id}', 'UserController@travelPlaces')->name('travelPlaces');
            Route::get('passenger/{id}', 'UserController@passenger')->name('passenger');
            Route::get('edit/{id}', 'UserController@edit')->name('edit');
            Route::post('update/{id}', 'UserController@update')->name('update');
            Route::get('destroy/{id}', 'UserController@destroy')->name('destroy');
            Route::get('destroyPassenger/{id}', 'UserController@destroyPassenger')->name('destroyPassenger');

            Route::get('drivers/confirmation', 'UserController@confirmation')->name('confirmation');
            Route::post('drivers/addCar', 'UserController@addCar')->name('addCar');

            Route::get('drivers/confirmation/confirm/{id}', 'UserController@confirmationConfirm')->name('confirmation.confirm');
            Route::get('drivers/confirmation/reject/{id}', 'UserController@confirmationReject')->name('confirmation.reject');


            Route::get('drivers/newCars', 'UserController@newCars')->name('newCars');
            Route::get('car/{id}', 'UserController@car')->name('car');
            Route::get('carDetail/{id}', 'UserController@carDetail')->name('carDetail');
            Route::get('drivers/newCar/confirm/{id}', 'UserController@newCarConfirm')->name('newCar.confirm');
            Route::get('drivers/newCar/reject/{id}', 'UserController@newCarReject')->name('newCar.reject');

            # Lodgers
            Route::get('lodgers', 'UserController@lodgers')->name('lodgers');
            Route::get('lodgers/confirmation', 'UserController@confirmationLodger')->name('confirmationLodger');
            Route::get('lodgers/confirmation/{id}/confirm', 'UserController@confirmLodger')->name('confirm.Lodger');
            Route::get('lodgers/confirmation/{id}/reject', 'UserController@rejectLodger')->name('reject.Lodger');
			Route::get('lodgers/{id}/edit', 'UserController@editLodger')->name('editLodger');
			Route::post('lodgers/{id}/update', 'UserController@saveLodger')->name('saveLodger');
			Route::get('lodgers/{id}/destroy', 'UserController@destroyLodger')->name('destroyLodger');
        });


        Route::name('city.')->prefix('city')->group(function () {
            Route::get('index', 'CityController@index')->name('index');
            Route::get('add', 'CityController@add')->name('add');
            Route::post('create', 'CityController@create')->name('create');
            Route::get('edit/{id}', 'CityController@edit')->name('edit');
            Route::post('update/{id}', 'CityController@update')->name('update');
            Route::get('destroy/{id}', 'CityController@destroy')->name('destroy');
        });

        Route::name('station.')->prefix('station')->group(function () {
            Route::get('index/{id}', 'StationController@index')->name('index');
            Route::get('add/{id}', 'StationController@add')->name('add');
            Route::post('create', 'StationController@create')->name('create');
            Route::get('edit/{id}', 'StationController@edit')->name('edit');
            Route::post('update/{id}', 'StationController@update')->name('update');
            Route::get('destroy/{id}', 'StationController@destroy')->name('destroy');
        });


        Route::name('travel.')->prefix('travel')->group(function () {
            Route::get('index/', 'TravelController@index')->name('index');
            Route::get('add/', 'TravelController@add')->name('add');
            Route::post('create', 'TravelController@create')->name('create');
            Route::get('edit/{id}', 'TravelController@edit')->name('edit');
            Route::post('update/{id}', 'TravelController@update')->name('update');
            Route::get('destroy/{id}', 'TravelController@destroy')->name('destroy');

            Route::get('carTypes', 'TravelController@carTypes')->name('carTypes');
            Route::get('addCarType', 'TravelController@addCarType')->name('addCarType');
            Route::get('carTypeDestroy', 'TravelController@carTypeDestroy')->name('carTypeDestroy');


        });


        Route::name('travel_station.')->prefix('travel_station')->group(function () {
            Route::get('index/{id}', 'TravelStationController@index')->name('index');
            Route::get('add/{id}', 'TravelStationController@add')->name('add');
            Route::post('create', 'TravelStationController@create')->name('create');
            Route::get('edit/{id}', 'TravelStationController@edit')->name('edit');
            Route::post('update/{id}', 'TravelStationController@update')->name('update');
            Route::get('destroy/{id}', 'TravelStationController@destroy')->name('destroy');
            Route::get('top/{id}', 'TravelStationController@top')->name('top');
            Route::get('bottom/{id}', 'TravelStationController@bottom')->name('bottom');
        });

        Route::any('setting', 'MainController@setting')->name('setting');
        Route::post('sendMultiple', 'MainController@sendMultiple');

        Route::resource('company', 'CompanyController');
		Route::get('company/{id}/destroy', 'CompanyController@destroy')->name('companyDelete');

        # Cars
        Route::name('car.')->prefix('cars')->group(function(){
            Route::get('/', 'CarController@index')->name('index');
            Route::get('/{id}/edit', 'CarController@edit')->name('edit');
            Route::post('/{id}/update', 'CarController@update')->name('update');
        });

        # Cashiers
        Route::name('cashier.')->prefix('cashiers')->group(function(){
            Route::get('/', 'CashierController@index')->name('index');
            Route::get('/{id}/edit', 'CashierController@edit')->name('edit');
            Route::post('/{id}/update', 'CashierController@update')->name('update');
        });

        Route::get('/firebase/send', 'CashierController@firebase');

        # Rest Places
        Route::resource('rest', 'RestController');
		
		Route::get('sold-tickets', 'UserController@soldTickets')->name('soldTickets');
    });
});
