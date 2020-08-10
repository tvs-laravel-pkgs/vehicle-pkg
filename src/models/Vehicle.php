<?php

namespace Abs\VehiclePkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\BaseModel;
use App\Company;
use App\VehicleModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPExcel_Style_NumberFormat;

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
		// "vin_number",
		"is_sold",
		"sold_date",
		"warranty_member_id",
		"ewp_expiry_date",
	];

	protected $casts = [
		"is_sold" => 'boolean',
	];

	protected static $excelColumnRules = [
		'Engine Number' => [
			'table_column_name' => 'engine_number',
			'rules' => [

			],
		],
		'Chassis Number' => [
			'table_column_name' => 'chassis_number',
			'rules' => [
				'required' => [
				],
			],
		],
		'Model Number' => [
			'table_column_name' => 'model_id',
			'rules' => [
				'fk' => [
					'class' => 'App\VehicleModel',
					'foreign_table_column' => 'model_number',
				],
			],
		],
		'Is Registered' => [
			'table_column_name' => 'is_registered',
			'rules' => [
				'required' => [
				],
				'unsigned_integer' => [
					'size' => '1',
				],
			],
		],
		'Registration Number' => [
			'table_column_name' => 'registration_number',
			'rules' => [],
		],
		/*'VIN Number' => [
			'table_column_name' => 'vin_number',
			'rules' => [],
		],*/
		'Sold Date' => [
			'table_column_name' => 'sold_date',
			'rules' => [],
		],
	];

	public static function saveFromObject($record_data) {
		$record = [
			'Company Code' => $record_data->company_code,
			'Engine Number' => $record_data->engine_number,
			'Chassis Number' => $record_data->chassis_number,
			'Model Number' => $record_data->model_number,
			'Is Registered' => $record_data->is_registered,
			'Registration Number' => $record_data->registration_number,
			// 'VIN Number' => $record_data->vin_number,
			'Sold Date' => $record_data->sold_date,
		];
		return static::saveFromExcelArray($record);
	}

	public static function saveFromExcelArray($record_data) {
		try {
			$is_registered = 0;
			$errors = [];
			$company = Company::where('code', $record_data['Company Code'])->first();
			if (!$company) {
				return [
					'success' => false,
					'errors' => ['Invalid Company : ' . $record_data['Company Code']],
				];
			}

			if (!isset($record_data['created_by_id'])) {
				$admin = $company->admin();

				if (!$admin) {
					return [
						'success' => false,
						'errors' => ['Default Admin user not found'],
					];
				}
				$created_by_id = $admin->id;
			} else {
				$created_by_id = $record_data['created_by_id'];
			}

			if (empty($record_data['Chassis Number'])) {
				$errors[] = 'Chassis Number is empty';
			}

			if (!empty($record_data['Model Number'])) {
				$model = VehicleModel::where([
					'company_id' => $company->id,
					'model_number' => $record_data['Model Number'],
				])->first();
				if (!$model) {
					$errors[] = 'Invalid Model Number : ' . $record_data['Model Number'];
				}
			}

			if (!empty($record_data['Sold Date'])) {
				$sold_date = PHPExcel_Style_NumberFormat::toFormattedString($record_data['Sold Date'], 'yyyy-mm-dd');
			} else {
				$sold_date = null;
			}

			if (!empty($record_data['Registration Number'])) {
				$is_registered = 1;
			}

			if (count($errors) > 0) {
				return [
					'success' => false,
					'errors' => $errors,
				];
			}

			$record = Self::firstOrNew([
				'company_id' => $company->id,
				'chassis_number' => $record_data['Chassis Number'],
			]);

			// dd($record_data)
			$result = Self::validateAndFillExcelColumns($record_data, Static::$excelColumnRules, $record);
			if (!$result['success']) {
				return $result;
			}
			// $record->engine_number = $record_data['Engine Number'];
			$record->is_registered = $is_registered;
			$record->sold_date = $sold_date;
			if ($record->sold_date) {
				$record->is_sold = 1;
			}
			$record->company_id = $company->id;
			$record->created_by_id = $created_by_id;
			$record->status_id = 8140;
			$record->save();
			return [
				'success' => true,
			];
		} catch (\Exception $e) {
			return [
				'success' => false,
				'errors' => [
					$e->getMessage(),
				],
			];
		}
	}
	// Getter & Setters --------------------------------------------------------------

	//APPEND - INBETWEEN REGISTRATION NUMBER
	public function getRegistrationNumberAttribute($value) {
		$registration_number = '';

		if ($value) {
			$value = str_replace('-', '', $value);
			$reg_number = str_split($value);

			$last_four_numbers = substr($value, -4);

			$registration_number .= $reg_number[0] . $reg_number[1] . '-' . $reg_number[2] . $reg_number[3] . '-';

			if (is_numeric($reg_number[4])) {
				$registration_number .= $last_four_numbers;
			} else {
				$registration_number .= $reg_number[4];
				if (is_numeric($reg_number[5])) {
					$registration_number .= '-' . $last_four_numbers;
				} else {
					$registration_number .= $reg_number[5] . '-' . $last_four_numbers;
				}
			}
		}
		return $this->attributes['registration_number'] = $registration_number;
	}

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

	public static function relationships($action = '') {
		$relationships = [
			'model',
		];
		return $relationships;
	}

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
				$query->orWhere('chassis_number', 'LIKE', '%' . $term . '%');
				$query->orWhere('registration_number', 'LIKE', '%' . $term . '%');
				$query->orWhere('engine_number', 'LIKE', '%' . $term . '%');
			});
			$query->company();
			// dd($query->toSql(), $query->get(), $term);
		}
	}

	// Static Operations --------------------------------------------------------------

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
