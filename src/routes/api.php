<?php
Route::group(['namespace' => 'App\Http\Controllers\Api', 'middleware' => ['auth:api']], function () {
	Route::group(['prefix' => 'api'], function () {
		Route::post('vehicle/save', 'VehicleController@saveVehicle');
	});
});