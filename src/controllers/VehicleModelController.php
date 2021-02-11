<?php

namespace Abs\VehiclePkg;
use App\Http\Controllers\Controller;
use App\VehicleModel;
use Auth;
use Carbon\Carbon;
use DB;
use Entrust;
use Illuminate\Http\Request;
use Validator;
use Yajra\Datatables\Datatables;

class VehicleModelController extends Controller {

	public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}

	public function getVehicleModelList(Request $request) {
		$vehicle_models = VehicleModel::withTrashed()

			->select([
				'vehicle_models.id',
				'vehicle_models.name',
				'vehicle_models.code',

				DB::raw('IF(vehicle_models.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('vehicle_models.company_id', Auth::user()->company_id)

			->where(function ($query) use ($request) {
				if (!empty($request->name)) {
					$query->where('vehicle_models.name', 'LIKE', '%' . $request->name . '%');
				}
			})
			->where(function ($query) use ($request) {
				if ($request->status == '1') {
					$query->whereNull('vehicle_models.deleted_at');
				} else if ($request->status == '0') {
					$query->whereNotNull('vehicle_models.deleted_at');
				}
			})
		;

		return Datatables::of($vehicle_models)
			->rawColumns(['name', 'action'])
			->addColumn('name', function ($vehicle_model) {
				$status = $vehicle_model->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $vehicle_model->name;
			})
			->addColumn('action', function ($vehicle_model) {
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$output = '';
				if (Entrust::can('edit-vehicle_model')) {
					$output .= '<a href="#!/vehicle-pkg/vehicle_model/edit/' . $vehicle_model->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';
				}
				if (Entrust::can('delete-vehicle_model')) {
					$output .= '<a href="javascript:;" data-toggle="modal" data-target="#vehicle_model-delete-modal" onclick="angular.element(this).scope().deleteVehicleModel(' . $vehicle_model->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';
				}
				return $output;
			})
			->make(true);
	}

	public function getVehicleModelFormData(Request $request) {
		$id = $request->id;
		if (!$id) {
			$vehicle_model = new VehicleModel;
			$action = 'Add';
		} else {
			$vehicle_model = VehicleModel::withTrashed()->find($id);
			$action = 'Edit';
		}
		$this->data['success'] = true;
		$this->data['vehicle_model'] = $vehicle_model;
		$this->data['action'] = $action;
		return response()->json($this->data);
	}

	public function saveVehicleModel(Request $request) {
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
					'min:3',
					'max:32',
					'unique:vehicle_models,code,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'name' => [
					'required:true',
					'min:3',
					'max:191',
					// 'unique:vehicle_models,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$vehicle_model = new VehicleModel;
				$vehicle_model->company_id = Auth::user()->company_id;
			} else {
				$vehicle_model = VehicleModel::withTrashed()->find($request->id);
			}
			$vehicle_model->fill($request->all());
			if ($request->status == 'Inactive') {
				$vehicle_model->deleted_at = Carbon::now();
			} else {
				$vehicle_model->deleted_at = NULL;
			}
			$vehicle_model->save();

			DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Vehicle Model Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Vehicle Model Updated Successfully',
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

	public function deleteVehicleModel(Request $request) {
		DB::beginTransaction();
		// dd($request->id);
		try {
			$vehicle_model = VehicleModel::withTrashed()->where('id', $request->id)->forceDelete();
			if ($vehicle_model) {
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Vehicle Model Deleted Successfully']);
			}
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}

	public function getVehicleModels(Request $request) {
		$vehicle_models = VehicleModel::withTrashed()
			->with([
				'vehicle-models',
				'vehicle-models.user',
			])
			->select([
				'vehicle_models.id',
				'vehicle_models.name',
				'vehicle_models.code',
				DB::raw('IF(vehicle_models.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('vehicle_models.company_id', Auth::user()->company_id)
			->get();

		return response()->json([
			'success' => true,
			'vehicle_models' => $vehicle_models,
		]);
	}
}