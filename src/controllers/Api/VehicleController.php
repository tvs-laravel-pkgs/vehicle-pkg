<?php

namespace Abs\VehiclePkg\Api;

use Abs\BasicPkg\Traits\CrudTrait;
use App\Http\Controllers\Controller;
use App\JobOrder;
use App\TradePlateNumber;
use App\User;
use App\Vehicle;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;

class VehicleController extends Controller {
	use CrudTrait;
	public $model = Vehicle::class;
	public $successStatus = 200;

	//VEHICLE SAVE
	public function saveVehicle(Request $request) {
		// dd($request->all());
		try {
			//REMOVE WHITE SPACE BETWEEN REGISTRATION NUMBER
			$request->registration_number = str_replace(' ', '', $request->registration_number);

			//REGISTRATION NUMBER VALIDATION
			$error = '';
			if ($request->registration_number) {
				$registration_no_count = strlen($request->registration_number);
				if ($registration_no_count < 10) {
					return response()->json([
						'success' => false,
						'error' => 'Validation Error',
						'errors' => [
							'The registration number must be at least 10 characters.',
						],
					]);
				} else {

					$registration_number = explode('-', $request->registration_number);

					if (count($registration_number) > 2) {
						$valid_reg_number = 1;
						if (!preg_match('/^[A-Z]+$/', $registration_number[0]) || !preg_match('/^[0-9]+$/', $registration_number[1])) {
							$valid_reg_number = 0;
						}

						if (count($registration_number) > 3) {
							if (!preg_match('/^[A-Z]+$/', $registration_number[2]) || strlen($registration_number[3]) != 4 || !preg_match('/^[0-9]+$/', $registration_number[3])) {
								$valid_reg_number = 0;
							}
						} else {
							if (!preg_match('/^[0-9]+$/', $registration_number[2]) || strlen($registration_number[2]) != 4) {
								$valid_reg_number = 0;
							}
						}
					} else {
						$valid_reg_number = 0;
					}

					if ($valid_reg_number == 0) {
						return response()->json([
							'success' => false,
							'error' => 'Validation Error',
							'errors' => [
								"Please enter valid registration number!",
							],
						]);
					}
				}
			}
			$request->registration_number = str_replace('-', '', $request->registration_number);

			$validator = Validator::make($request->all(), [
				'job_order_id' => [
					'required',
					'integer',
					'exists:job_orders,id',
				],
				'is_registered' => [
					'required',
					'integer',
				],
				'registration_number' => [
					'required_if:is_registered,==,1',
					'max:13',
					'unique:vehicles,registration_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'is_sold' => [
					'required_if:is_registered,==,0',
					'integer',
				],
				'sold_date' => [
					'required_if:is_sold,==,1',
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
				// 'vin_number' => [
				// 	'required',
				// 	'min:17',
				// 	'max:17',
				// 	'string',
				// 	'unique:vehicles,vin_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				// ],
			]);

			if ($validator->fails()) {
				return response()->json([
					'success' => false,
					'error' => 'Validation Error',
					'errors' => $validator->errors()->all(),
				]);
			}

			DB::beginTransaction();

			// INWARD PROCESS CHECK - VEHICLE DETAIL
			$job_order = JobOrder::find($request->job_order_id);

			//VEHICLE GATE ENTRY DETAILS
			// UNREGISTRED VEHICLE
			if ($request->is_registered != 1) {
				if ($request->plate_number) {
					$trade_plate_number = TradePlateNumber::firstOrNew([
						'company_id' => Auth::user()->company_id,
						'outlet_id' => Auth::user()->employee->outlet_id,
						'trade_plate_number' => $request->plate_number,
					]);

					if (!$trade_plate_number->exists) {
						$trade_plate_number->created_by_id = Auth::user()->id;
						$trade_plate_number->created_at = Carbon::now();
					} else {
						$trade_plate_number->updated_by_id = Auth::user()->id;
						$trade_plate_number->updated_at = Carbon::now();
					}

					$trade_plate_number->save();

					$job_order->gatein_trade_plate_number_id = $trade_plate_number->id;
				}
			} else {
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
				$vehicle->registration_number = $request->registration_number;
				if ($vehicle->currentOwner) {
					$vehicle->status_id = 8142; //COMPLETED
				} else {
					$vehicle->status_id = 8141; //CUSTOMER NOT MAPPED
				}
				$vehicle->save();
			}

			$job_order->status_id = 8463;
			$job_order->save();

			$job_order->inwardProcessChecks()->where('tab_id', 8700)->update(['is_form_filled' => 1]);

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
