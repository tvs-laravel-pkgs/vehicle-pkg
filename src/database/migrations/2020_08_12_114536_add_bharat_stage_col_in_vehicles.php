<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBharatStageColInVehicles extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('vehicles', function (Blueprint $table) {
			$table->unsignedInteger('bharat_stage_id')->nullable()->after('sold_date');
			$table->foreign("bharat_stage_id")->references("id")->on("bharat_stages")->onDelete("SET NULL")->onUpdate("CASCADE");

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('vehicles', function (Blueprint $table) {
			$table->dropForeign("vehicles_bharat_stage_id_foreign");
			$table->dropColumn('bharat_stage_id');

		});
	}
}
