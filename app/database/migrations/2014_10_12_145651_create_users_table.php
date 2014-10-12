<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create user table
		DB::statement('CREATE TABLE IF NOT EXISTS `user` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		  `secret` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
		  `privilege` int(10) unsigned NOT NULL,
		  `status` tinyint(3) unsigned NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		// Create user auth table
		DB::statement('CREATE TABLE IF NOT EXISTS `user_auth` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `type` tinyint(3) unsigned NOT NULL,
		  `value` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
		  `user_id` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		// Create user session table
		DB::statement('CREATE TABLE IF NOT EXISTS `user_session` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
		  `expires` int(10) unsigned NOT NULL,
		  `status` tinyint(3) unsigned NOT NULL,
		  `user_id` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
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
