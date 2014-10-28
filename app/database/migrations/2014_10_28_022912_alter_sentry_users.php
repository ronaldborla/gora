<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSentryUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		@DB::statement('ALTER TABLE  `users` DROP  `email`');
		@DB::statement('ALTER TABLE  `users` ADD  `mobile` VARCHAR( 10 ) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL AFTER  `id` , ADD UNIQUE ( `mobile` )');
		@DB::statement('ALTER TABLE  `users` ADD  `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER  `last_name`');
		@DB::statemet('ALTER TABLE  `users` ADD  `location` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER  `name`');
		@DB::statemet('ALTER TABLE  `users` ADD  `bounds` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER  `location`');
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
