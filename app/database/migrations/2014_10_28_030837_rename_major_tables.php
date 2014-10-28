<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameMajorTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Rename
		@DB::statement('RENAME TABLE `category` TO `categories`');
		@DB::statement('RENAME TABLE `contact` TO `contacts`');
		@DB::statement('RENAME TABLE `establishment` TO `establishments`');
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
