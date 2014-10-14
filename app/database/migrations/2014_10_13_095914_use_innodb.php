<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UseInnodb extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Use InnoDB
		DB::statement('ALTER TABLE `user` ENGINE = InnoDB');
		DB::statement('ALTER TABLE `user_auth` ENGINE = InnoDB');
		DB::statement('ALTER TABLE `user_session` ENGINE = InnoDB');
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