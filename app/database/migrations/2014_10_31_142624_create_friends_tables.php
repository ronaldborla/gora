<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendsTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('CREATE TABLE IF NOT EXISTS `friends` (
			`friend_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL,
			PRIMARY KEY (`friend_id`,`user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

		DB::statement('CREATE TABLE IF NOT EXISTS `friends_group` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` text NOT NULL,
			`user_id` int(11) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1;');

		
		DB::statement('CREATE TABLE IF NOT EXISTS `friends_lkp` (
			`id` int(11) NOT NULL,
			`friend_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL,
			`group_id` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('friends');
	}

}
