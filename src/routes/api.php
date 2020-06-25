<?php
Route::group(['namespace' => 'App\Http\Controllers\Api', 'middleware' => ['auth:api']], function () {
	Route::group(['prefix' => 'api'], function () {
		Route::post('vehicle/save', 'VehicleController@saveVehicle');

		Route::group(['prefix' => 'vehicle'], function () {
			$controller = 'Vehicle';
			Route::get('index', $controller . 'Controller@index');
			Route::get('read/{id}', $controller . 'Controller@read');
			Route::post('save-from-form-data', $controller . 'Controller@saveFromFormData');
			Route::post('save-from-ng-data', $controller . 'Controller@saveFromNgData');
			Route::post('remove', $controller . 'Controller@remove');
			Route::get('options', $controller . 'Controller@options');
		});

	});
});