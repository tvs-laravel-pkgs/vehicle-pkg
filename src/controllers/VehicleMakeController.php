<?php

namespace Abs\VehiclePkg;
use App\Http\Controllers\Controller;
use App\VehicleMake;
use Auth;
use Carbon\Carbon;
use DB;
use Entrust;
use Illuminate\Http\Request;
use Validator;
use Yajra\Datatables\Datatables;

class VehicleMakeController extends Controller {

	public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}

	public function getVehicleMakeList(Request $request) {
		$vehicle_makes = VehicleMake::withTrashed()

			->select([
				'vehicle_makes.id',
				'vehicle_makes.name',
				'vehicle_makes.code',

				DB::raw('IF(vehicle_makes.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('vehicle_makes.company_id', Auth::user()->company_id)

			->where(function ($query) use ($request) {
				if (!empty($request->name)) {
					$query->where('vehicle_makes.name', 'LIKE', '%' . $request->name . '%');
				}
			})
			->where(function ($query) use ($request) {
				if ($request->status == '1') {
					$query->whereNull('vehicle_makes.deleted_at');
				} else if ($request->status == '0') {
					$query->whereNotNull('vehicle_makes.deleted_at');
				}
			})
		;

		return Datatables::of($vehicle_makes)
			->rawColumns(['name', 'action'])
			->addColumn('name', function ($vehicle_make) {
				$status = $vehicle_make->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $vehicle_make->name;
			})
			->addColumn('action', function ($vehicle_make) {
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$output = '';
				if (Entrust::can('edit-vehicle-make')) {
					$output .= '<a href="#!/vehicle-pkg/vehicle-make/edit/' . $vehicle_make->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';
				}
				if (Entrust::can('delete-vehicle-make')) {
					$output .= '<a href="javascript:;" data-toggle="modal" data-target="#vehicle-make-delete-modal" onclick="angular.element(this).scope().deleteVehicleMake(' . $vehicle_make->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';
				}
				return $output;
			})
			->make(true);
	}

	public function getVehicleMakeFormData(Request $request) {
		$id = $request->id;
		if (!$id) {
			$vehicle_make = new VehicleMake;
			$action = 'Add';
		} else {
			$vehicle_make = VehicleMake::withTrashed()->find($id);
			$action = 'Edit';
		}
		$this->data['success'] = true;
		$this->data['vehicle_make'] = $vehicle_make;
		$this->data['action'] = $action;
		return response()->json($this->data);
	}

	public function saveVehicleMake(Request $request) {
		// dd($request->all());
		try {
			$error_messages = [
				'code.required' => 'Short Name is Required',
				'code.unique' => 'Short Name is already taken',
				'code.min' => 'Short Name is Minimum 3 Charachers',
				'code.max' => 'Short Name is Maximum 32 Charachers',
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
				'name.min' => 'Name is Minimum 3 Charachers',
				'name.max' => 'Name is Maximum 191 Charachers',
			];
			$validator = Validator::make($request->all(), [
				'code' => [
					'required:true',
					'min:2',
					'max:32',
					'unique:vehicle_makes,code,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'name' => [
					'required:true',
					'min:3',
					'max:191',
					'unique:vehicle_makes,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$vehicle_make = new VehicleMake;
				$vehicle_make->company_id = Auth::user()->company_id;
			} else {
				$vehicle_make = VehicleMake::withTrashed()->find($request->id);
			}
			$vehicle_make->fill($request->all());
			if ($request->status == 'Inactive') {
				$vehicle_make->deleted_at = Carbon::now();
			} else {
				$vehicle_make->deleted_at = NULL;
			}
			$vehicle_make->save();

			DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Vehicle Make Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Vehicle Make Updated Successfully',
				]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'error' => $e->getMessage(),
			]);
		}
	}

	public function deleteVehicleMake(Request $request) {
		DB::beginTransaction();
		// dd($request->id);
		try {
			$vehicle_make = VehicleMake::withTrashed()->where('id', $request->id)->forceDelete();
			if ($vehicle_make) {
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Vehicle Make Deleted Successfully']);
			}
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}

	public function getVehicleMakes(Request $request) {
		$vehicle_makes = VehicleMake::withTrashed()
			->with([
				'vehicle-makes',
				'vehicle-makes.user',
			])
			->select([
				'vehicle_makes.id',
				'vehicle_makes.name',
				'vehicle_makes.code',
				DB::raw('IF(vehicle_makes.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('vehicle_makes.company_id', Auth::user()->company_id)
			->get();

		return response()->json([
			'success' => true,
			'vehicle_makes' => $vehicle_makes,
		]);
	}
}