<?php

Route::group(['namespace' => 'Abs\VehiclePkg', 'middleware' => ['web', 'auth'], 'prefix' => 'vehicle-pkg'], function () {

	//Vehicle Segment
	Route::get('/vehicle-segment/get-list', 'VehicleSegmentController@getVehicleSegmentList')->name('getVehicleSegmentList');
	Route::get('/vehicle-segment/get-form-data', 'VehicleSegmentController@getVehicleSegmentFormData')->name('getVehicleSegmentFormData');
	Route::post('/vehicle-segment/save', 'VehicleSegmentController@saveVehicleSegment')->name('saveVehicleSegment');
	Route::get('/vehicle-segment/delete', 'VehicleSegmentController@deleteVehicleSegment')->name('deleteVehicleSegment');
	Route::get('/vehicle-segment/get-filter-data', 'VehicleSegmentController@getVehicleSegmentFilter')->name('getVehicleSegmentFilter');

	//Vehicle Make
	Route::get('/vehicle-make/get-list', 'VehicleMakeController@getVehicleMakeList')->name('getVehicleMakeList');
	Route::get('/vehicle-make/get-form-data', 'VehicleMakeController@getVehicleMakeFormData')->name('getVehicleMakeFormData');
	Route::post('/vehicle-make/save', 'VehicleMakeController@saveVehicleMake')->name('saveVehicleMake');
	Route::get('/vehicle-make/delete', 'VehicleMakeController@deleteVehicleMake')->name('deleteVehicleMake');
	Route::get('/vehicle-make/get-filter-data', 'VehicleMakeController@getVehicleMakeFilterData')->name('getVehicleMakeFilterData');

	//Vehicle Model
	Route::get('/vehicle-model/get-list', 'VehicleModelController@getVehicleModelList')->name('getVehicleModelList');
	Route::get('/vehicle-model/get-form-data', 'VehicleModelController@getVehicleModelFormData')->name('getVehicleModelFormData');
	Route::post('/vehicle-model/save', 'VehicleModelController@saveVehicleModel')->name('saveVehicleModel');
	Route::get('/vehicle-model/delete', 'VehicleModelController@deleteVehicleModel')->name('deleteVehicleModel');
	Route::get('/vehicle-model/get-filter-data', 'VehicleModelController@getVehicleModelFilterData')->name('getVehicleModelFilterData');

});