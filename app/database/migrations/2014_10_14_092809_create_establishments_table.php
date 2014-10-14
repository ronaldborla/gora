<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstablishmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create establishment table
		DB::statement('CREATE TABLE IF NOT EXISTS `establishment` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
		  `address` text COLLATE utf8_unicode_ci NOT NULL,
		  `lat` double NOT NULL,
		  `lng` double NOT NULL,
		  `price_min` int(10) unsigned NOT NULL,
		  `price_max` int(10) unsigned NOT NULL,
		  `tags` text COLLATE utf8_unicode_ci NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `user_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		// Create category table
		DB::statement('CREATE TABLE IF NOT EXISTS `category` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
		  `description` text COLLATE utf8_unicode_ci NOT NULL,
		  `tags` text COLLATE utf8_unicode_ci NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `parent` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		// Create category list table
		DB::statement('CREATE TABLE IF NOT EXISTS `category_list` (
		  `order` tinyint(3) unsigned NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `category_id` int(10) unsigned NOT NULL,
		  `establishment_id` int(10) unsigned NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		// Create contact table
		DB::statement('CREATE TABLE IF NOT EXISTS `contact` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `type` tinyint(3) unsigned NOT NULL,
		  `value` text COLLATE utf8_unicode_ci NOT NULL,
		  `description` text COLLATE utf8_unicode_ci NOT NULL,
		  `order` tinyint(3) unsigned NOT NULL,
		  `primary` tinyint(3) unsigned NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `establishment_id` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		// Create product table
		DB::statement('CREATE TABLE IF NOT EXISTS `product` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
		  `description` text COLLATE utf8_unicode_ci NOT NULL,
		  `tags` text COLLATE utf8_unicode_ci NOT NULL,
		  `order` tinyint(3) unsigned NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `establishment_id` int(10) unsigned NOT NULL,
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
