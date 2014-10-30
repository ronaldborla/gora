<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReservationsAndSubscriptions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create tables
		DB::statement('CREATE TABLE IF NOT EXISTS `reservations` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `members` text COLLATE utf8_unicode_ci NOT NULL,
		  `status` tinyint(3) unsigned NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
		  `establishment_id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		DB::statement('CREATE TABLE IF NOT EXISTS `subscriptions` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `type` tinyint(3) unsigned NOT NULL,
		  `options` text COLLATE utf8_unicode_ci NOT NULL,
		  `status` tinyint(3) unsigned NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
		  `establishment_id` int(10) unsigned NOT NULL,
		  `user_id` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
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
