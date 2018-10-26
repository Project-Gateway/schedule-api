<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test', function() {
    return 'test';
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/clients', 'ClientsController@index');
    Route::get('/clients/{client}', 'ClientsController@show');
    Route::put('/clients/{client}', 'ClientsController@update');

    Route::get('/appointments/{date}', 'AppointmentsController@servicesByDate')->where('date', '^\d{4}-\d{2}-\d{2}');
    Route::get('/availability/{date}/{provider?}', 'AppointmentsController@availability')->where('date', '^\d{4}-\d{2}-\d{2}');
    Route::post('/appointments/schedule', 'AppointmentsController@schedule');

    Route::get('/working-times/{year}/{week}','WorkingTimesController@byWeek')
        ->where(['year' => '^[0-9]{4}$', 'week' => '^[0-9]+$']);

    Route::post('/working-times', 'WorkingTimesController@store');

    Route::put('/working-times/{workingTime}', 'WorkingTimesController@update');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
