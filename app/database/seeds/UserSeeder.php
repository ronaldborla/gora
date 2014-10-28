<?php

  /**
   * User seeder
   */

  class UserSeeder extends Seeder {

    /**
     * Run
     */
    public function run() {

      // Truncated
      $this->command->info('Creating users..');

      // Truncate
      DB::table('users')->truncate();
      DB::table('users_groups')->truncate();
      DB::table('throttle')->truncate();

      // Groups
      $groups = array(
        Sentry::findGroupById(1), // Admins
        Sentry::findGroupById(2), // Clients
        Sentry::findGroupById(3)  // Members
      );

      // Set password
      $password = '123456';

      // Users
      $users = array(
        array(
          'first_name'=> 'Ronald',
          'last_name'=> 'Borla',
          'name'=> 'Ronald',
          'mobile'=> '9087800765'
        ),
        array(
          'first_name'=> 'Abzkan',
          'last_name'=> 'Abdul',
          'name'=> 'Abzkan',
          'mobile'=> '9298182485'
        ),
        array(
          'first_name'=> 'Brylle',
          'last_name'=> 'Cambronero',
          'name'=> 'Brylle',
          'mobile'=> '9223682399'
        )
      );

      foreach ($users as $i=> $user) {
        // Add user password and activated
        $user['password'] = $password;
        $user['activated'] = true;
        // Create user
        $user = Sentry::createUser($user);
        // Add groups
        for ($j = $i; $j < 3; $j++) {
          // Add group
          $user->addGroup($groups[$j]);
        }
      }

      // Created
      $this->command->info('Users created!');

    }
  }