<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchesAndSessions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create searches
		DB::statement('CREATE TABLE IF NOT EXISTS `searches` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `query` text COLLATE utf8_unicode_ci NOT NULL,
		  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `bounds` text COLLATE utf8_unicode_ci NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
		  `user_id` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		// Create sessions
		DB::statement('CREATE TABLE IF NOT EXISTS `user_sessions` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		  `options` text COLLATE utf8_unicode_ci NOT NULL,
		  `expires` int(10) unsigned NOT NULL,
		  `status` tinyint(3) unsigned NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
		  `user_id` int(11) NOT NULL,
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
