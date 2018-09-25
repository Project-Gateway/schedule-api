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

Route::group(['middleware' => ['auth']], function () {
    Route::get('/clients', 'ClientsController@index');
    Route::get('/clients/{client}', 'ClientsController@show');
    Route::put('/clients/{client}', 'ClientsController@update');

    Route::get('/appointments/{date}', 'AppointmentsController@servicesByDate')->where('date', '^\d{4}-\d{2}-\d{2}');
    Route::get('/availability/{date}/{provider?}', 'AppointmentsController@availability')->where('date', '^\d{4}-\d{2}-\d{2}');
    Route::post('/appointments/schedule', 'AppointmentsController@schedule');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
