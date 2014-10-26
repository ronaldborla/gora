<?php
class GroupsTableSeeder extends Seeder {

	public function run()
	{
		foreach(range(1, 10) as $index)
		{

            DB::table('groups')->truncate();

            $group = Sentry::createGroup(array(
                'name'        => 'administrator'
                ));
            $group = Sentry::createGroup(array(
                'name'        => 'client'
                ));
            $group = Sentry::createGroup(array(
                'name'        => 'member'
                ));
		}
	}

}