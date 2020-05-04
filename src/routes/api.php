<?php
Route::group(['namespace' => 'Abs\VehiclePkg\Api', 'middleware' => ['api', 'auth:api']], function () {
	Route::group(['prefix' => 'api/vehicle-pkg'], function () {
		//Route::post('punch/status', 'PunchController@status');
	});
});