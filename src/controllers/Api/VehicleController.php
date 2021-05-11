<?php

namespace Abs\VehiclePkg\Api;

use Abs\AmcPkg\AmcPolicy;
use Abs\BasicPkg\Traits\CrudTrait;
use Abs\GigoPkg\AmcAggregateCoupon;
use Abs\GigoPkg\AmcCustomer;
use Abs\GigoPkg\AmcMember;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WpoSoapController;
use App\JobOrder;
use App\User;
use App\Vehicle;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;

class VehicleController extends Controller
{
    use CrudTrait;
    public $model = Vehicle::class;
    public $successStatus = 200;

    public function __construct(WpoSoapController $getSoap = null)
    {
        $this->data['theme'] = config('custom.theme');
        $this->getSoap = $getSoap;
        $this->success_code = 200;
        $this->permission_denied_code = 401;
    }

    //VEHICLE SAVE
    public function saveVehicle(Request $request)
    {
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

            $request['registration_number'] = $request->registration_number ? str_replace('-', '', $request->registration_number) : null;

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
                    // 'unique:vehicles,registration_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
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
                    'min:8',
                    'max:64',
                    'string',
                    'unique:vehicles,chassis_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
                ],
                // 'vin_number' => [
                //     'required',
                //     'min:17',
                //     'max:17',
                //     'string',
                //     'unique:vehicles,vin_number,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
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
                if ($request->trade_plate_number) {
                    // $trade_plate_number = TradePlateNumber::firstOrNew([
                    //     'company_id' => Auth::user()->company_id,
                    //     'outlet_id' => Auth::user()->employee->outlet_id,
                    //     'trade_plate_number' => $request->plate_number,
                    // ]);

                    // if (!$trade_plate_number->exists) {
                    //     $trade_plate_number->created_by_id = Auth::user()->id;
                    //     $trade_plate_number->created_at = Carbon::now();
                    // } else {
                    //     $trade_plate_number->updated_by_id = Auth::user()->id;
                    //     $trade_plate_number->updated_at = Carbon::now();
                    // }

                    // $trade_plate_number->save();

                    $job_order->gatein_trade_plate_number_id = $request->trade_plate_number;
                }
            }

            $request['registration_number'] = $request->registration_number ? str_replace('-', '', $request->registration_number) : null;

            // else {
            //ONLY FOR REGISTRED VEHICLE
            if (!$request->id) {
                //NEW VEHICLE
                $vehicle = new Vehicle;
                $vehicle->company_id = Auth::user()->company_id;
                $vehicle->created_by_id = Auth::id();
                $vehicle->created_at = Carbon::now();
            } else {
                $vehicle = Vehicle::find($request->id);
                $vehicle->updated_by_id = Auth::id();
                $vehicle->updated_at = Carbon::now();
            }
            $vehicle->fill($request->all());
            if ($vehicle->currentOwner) {
                $vehicle->status_id = 8142; //COMPLETED
                $job_order->customer_id = $vehicle->currentOwner->customer_id;
                $job_order->inwardProcessChecks()->where('tab_id', 8701)->update(['is_form_filled' => 1]);
            } else {
                $vehicle->status_id = 8141; //CUSTOMER NOT MAPPED
            }
            $vehicle->save();

            // }

            $job_order->status_id = 8463;
            $job_order->save();

            $job_order->inwardProcessChecks()->where('tab_id', 8700)->update(['is_form_filled' => 1]);

            if (!$job_order->service_policy_id) {
                if ($vehicle->chassis_number) {
                    $soap_number = $vehicle->chassis_number;
                } elseif ($vehicle->engine_number) {
                    $soap_number = $vehicle->engine_number;
                } else {
                    $soap_number = $vehicle->registration_number;
                }

                $membership_data = $this->getSoap->GetTVSONEVehicleDetails($soap_number);

                if ($membership_data && $membership_data['success'] == 'true') {
                    // dump($membership_data);
                    $amc_customer_id = null;
                    if ($membership_data['tvs_one_customer_code']) {
                        $amc_customer = AmcCustomer::firstOrNew(['tvs_one_customer_code' => $membership_data['tvs_one_customer_code']]);

                        if (!$amc_customer->customer_id) {
                            $customer = Customer::where('code', ltrim($membership_data['al_dms_code'], '0'))->first();
                            if ($customer) {
                                $amc_customer->customer_id = $customer->id;
                            }
                        }

                        if ($amc_customer->exists) {
                            $amc_customer->updated_by_id = Auth::user()->id;
                            $amc_customer->updated_at = Carbon::now();
                        } else {
                            $amc_customer->created_by_id = Auth::user()->id;
                            $amc_customer->created_at = Carbon::now();
                            $amc_customer->updated_at = null;
                        }

                        $amc_customer->save();

                        $amc_customer_id = $amc_customer->id;

                        //Save Aggregate Coupons
                        if ($membership_data['aggregate_coupon']) {
                            $aggregate_coupons = explode(',', $membership_data['aggregate_coupon']);
                            if (count($aggregate_coupons) > 0) {
                                foreach ($aggregate_coupons as $aggregate_coupon) {
                                    $coupon = AmcAggregateCoupon::firstOrNew(['coupon_code' => str_replace(' ', '', $aggregate_coupon)]);
                                    if ($coupon->exists) {
                                        $coupon->updated_by_id = Auth::user()->id;
                                        $coupon->updated_at = Carbon::now();
                                    } else {
                                        $coupon->created_by_id = Auth::user()->id;
                                        $coupon->created_at = Carbon::now();
                                        $coupon->updated_at = null;
                                        $coupon->status_id = 1;
                                    }
                                    $coupon->amc_customer_id = $amc_customer->id;
                                    $coupon->save();
                                }
                            }
                        }
                    }

                    $amc_policy = AmcPolicy::firstOrNew(['company_id' => Auth::user()->company_id, 'name' => $membership_data['membership_name'], 'type' => $membership_data['membership_type']]);
                    if ($amc_policy->exists) {
                        $amc_policy->updated_by_id = Auth::user()->id;
                        $amc_policy->updated_at = Carbon::now();
                    } else {
                        $amc_policy->created_by_id = Auth::user()->id;
                        $amc_policy->created_at = Carbon::now();
                    }
                    $amc_policy->save();

                    $amc_member = AmcMember::firstOrNew(['company_id' => Auth::user()->company_id, 'entity_type_id' => 11180, 'vehicle_id' => $vehicle->id, 'policy_id' => $amc_policy->id, 'number' => $membership_data['membership_number']]);

                    if ($amc_member->exists) {
                        $amc_member->updated_by_id = Auth::user()->id;
                        $amc_member->updated_at = Carbon::now();
                    } else {
                        $amc_member->created_by_id = Auth::user()->id;
                        $amc_member->created_at = Carbon::now();
                    }

                    $amc_member->start_date = date('Y-m-d', strtotime($membership_data['start_date']));
                    $amc_member->expiry_date = date('Y-m-d', strtotime($membership_data['end_date']));
                    $amc_member->amc_customer_id = $amc_customer_id;

                    $amc_member->save();

                    $job_order->service_policy_id = $amc_member->id;
                    $job_order->save();
                }
            }

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
