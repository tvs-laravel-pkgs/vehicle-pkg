<?php

namespace Abs\VehiclePkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\BaseModel;
use App\Company;
use App\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends BaseModel {
	use SeederTrait;
	use SoftDeletes;
	protected $table = 'vehicles';
	public $timestamps = true;

	protected $fillable = [
		"company_id",
		"engine_number",
		"chassis_number",
		"model_id",
		"is_registered",
		"registration_number",
		"vin_number",
		"is_sold",
		"sold_date",
		"warranty_member_id",
		"ewp_expiry_date",
	];

	public function getDateOfJoinAttribute($value) {
		return empty($value) ? '' : date('d-m-Y', strtotime($value));
	}

	public function setDateOfJoinAttribute($date) {
		return $this->attributes['date_of_join'] = empty($date) ? NULL : date('Y-m-d', strtotime($date));
	}

	public function getSoldDateAttribute($value) {
		return empty($value) ? '' : date('d-m-Y', strtotime($value));
	}

	public function setSoldDateAttribute($date) {
		return $this->attributes['sold_date'] = empty($date) ? NULL : date('Y-m-d', strtotime($date));
	}

	// Relationships --------------------------------------------------------------

	public function vehicleOwners() {
		return $this->hasMany('App\VehicleOwner', 'vehicle_id', 'id');
	}

	public function currentOwner() {
		return $this->hasOne('App\VehicleOwner', 'vehicle_id')->orderBy('from_date', 'DESC');
	}

	public function model() {
		return $this->belongsTo('App\VehicleModel', 'model_id');
	}

	public function jobOrders() {
		return $this->hasMany('App\JobOrder');
	}

	public function lastJobOrder() {
		return $this->hasOne('App\JobOrder')->orderBy('created_at', 'DESC')->skip(1)->take(1);
	}

	public function status() {
		return $this->belongsTo('App\Config', 'status_id');
	}

	// Query Scopes --------------------------------------------------------------

	public function scopeFilterSearch($query, $term) {
		if (strlen($term)) {
			$query->where(function ($query) use ($term) {
				$query->orWhere('code', 'LIKE', '%' . $term . '%');
				$query->orWhere('name', 'LIKE', '%' . $term . '%');
			});
		}
	}

	// Static Operations --------------------------------------------------------------

	public static function createFromObject($record_data) {

		$errors = [];
		$company = Company::where('code', $record_data->company)->first();
		if (!$company) {
			dump('Invalid Company : ' . $record_data->company);
			return;
		}

		$admin = $company->admin();
		if (!$admin) {
			dump('Default Admin user not found');
			return;
		}

		$type = Config::where('name', $record_data->type)->where('config_type_id', 89)->first();
		if (!$type) {
			$errors[] = 'Invalid Tax Type : ' . $record_data->type;
		}

		if (count($errors) > 0) {
			dump($errors);
			return;
		}

		$record = self::firstOrNew([
			'company_id' => $company->id,
			'name' => $record_data->tax_name,
		]);
		$record->type_id = $type->id;
		$record->created_by_id = $admin->id;
		$record->save();
		return $record;
	}

	public static function getList($params = [], $add_default = true, $default_text = 'Select Vehicle') {
		$list = Collect(Self::select([
			'id',
			'name',
		])
				->orderBy('name')
				->get());
		if ($add_default) {
			$list->prepend(['id' => '', 'name' => $default_text]);
		}
		return $list;
	}

}
