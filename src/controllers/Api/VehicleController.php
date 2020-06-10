<?php

namespace Abs\VehiclePkg\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\Vehicle;
use Auth;
use DB;
use Illuminate\Http\Request;
use Validator;

class VehicleController extends Controller {
	public $successStatus = 200;

	//VEHICLE SAVE
	public function saveVehicle(Request $request) {
		try {
			//REMOVE WHITE SPACE BETWEEN REGISTRATION NUMBER
			$request->registration_number = str_replace(' ', '', $request->registration_number);

			//REGISTRATION NUMBER VALIDATION
			$error = '';
			if ($request->registration_number) {
				$registration_no_count = strlen($request->registration_number);
				if ($registration_no_count < 8) {
					return response()->json([
						'success' => false,
						'error' => 'Validation Error',
						'errors' => [
							'The registration number must be at least 8 characters.',
						],
					]);
				} else {
					$first_two_string = substr($request->registration_number, 0, 2);
					$next_two_number = substr($request->registration_number, 2, 2);
					$last_two_number = substr($request->registration_number, -2);
					if (!preg_match('/^[A-Z]+$/', $first_two_string) && !preg_match('/^[0-9]+$/', $next_two_number) && !preg_match('/^[0-9]+$/', $last_two_number)) {
						$error = "Please enter valid registration number!";
					}
					if ($error) {
						return response()->json([
							'success' => false,
							'error' => 'Validation Error',
							'errors' => [
								$error,
							],
						]);
					}
				}
			}

			$validator = Validator::make($request->all(), [
				'is_registered' => [
					'required',
					'integer',
				],
				'registration_number' => [
					'required_if:is_registered,==,1',
					'max:10',
					'unique:vehicles,registration_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'model_id' => [
					'required',
					'exists:models,id',
					'integer',
				],
				'engine_number' => [
					'required',
					'min:7',
					'max:64',
					'string',
					'unique:vehicles,engine_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'chassis_number' => [
					'required',
					'min:10',
					'max:64',
					'string',
					'unique:vehicles,chassis_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'vin_number' => [
					'required',
					'min:17',
					'max:32',
					'string',
					'unique:vehicles,vin_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			]);

			if ($validator->fails()) {
				return response()->json([
					'success' => false,
					'error' => 'Validation Error',
					'errors' => $validator->errors()->all(),
				]);
			}

			DB::beginTransaction();
			//VEHICLE GATE ENTRY DETAILS
			// UNREGISTRED VEHICLE DIFFERENT FLOW WAITING FOR REQUIREMENT
			if ($request->is_registered != 1) {
				return response()->json([
					'success' => false,
					'error' => 'Validation Error',
					'errors' => [
						'Unregistred Vehile Not allow!!',
					],
				]);
			}

			//ONLY FOR REGISTRED VEHICLE
			if (!$request->id) {
				//NEW VEHICLE
				$vehicle = new Vehicle;
				$vehicle->company_id = Auth::user()->company_id;
				$vehicle->created_by_id = Auth::id();
			} else {
				$vehicle = Vehicle::find($request->id);
				$vehicle->updated_by_id = Auth::id();
			}
			$vehicle->fill($request->all());
			$vehicle->status_id = 8141; //CUSTOMER NOT MAPPED
			$vehicle->save();

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => 'Vehicle detail saved Successfully!!',
			]);

		} catch (Exception $e) {
			return response()->json([
				'success' => false,
				'error' => 'Server Network Down!',
				'errors' => ['Exception Error' => $e->getMessage()],
			]);
		}
	}
}
