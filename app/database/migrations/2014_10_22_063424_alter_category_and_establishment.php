<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCategoryAndEstablishment extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Alter tables
		DB::statement('ALTER TABLE  `category` CHANGE  `parent`  `parent_id` INT( 10 ) UNSIGNED NOT NULL');
		DB::statement('ALTER TABLE  `establishment` ADD FULLTEXT (`tags`)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
