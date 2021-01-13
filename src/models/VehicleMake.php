<?php

namespace Abs\VehiclePkg;

use Abs\BasicPkg\Models\BaseModel;
use Abs\HelperPkg\Traits\SeederTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMake extends BaseModel {
	use SeederTrait;
	use SoftDeletes;
	protected $table = 'vehicle_makes';
	public $timestamps = true;
	protected $fillable =
		["id", "company_id", "code", "name"]
	;

	protected static $excelColumnRules = [
		'Code' => [
			'table_column_name' => 'code',
			'rules' => [
				'required' => [
				],
			],
		],
		'Name' => [
			'table_column_name' => 'name',
			'rules' => [
				'required' => [
				],
			],
		],
	];

	// Getter & Setters --------------------------------------------------------------

	// Relations --------------------------------------------------------------

	// Static operations --------------------------------------------------------------

}
