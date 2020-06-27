<?php

namespace Abs\VehiclePkg;

use Abs\BasicPkg\BaseModel;
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

	// Getter & Setters --------------------------------------------------------------

	// Relations --------------------------------------------------------------

	// Static operations --------------------------------------------------------------

}
