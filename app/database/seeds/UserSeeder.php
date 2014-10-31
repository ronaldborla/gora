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
          'first_name'=> 'Brylle',
          'last_name'=> 'Cambronero',
          'name'=> 'Brylle Cambronero',
          'mobile'=> '9223682399'
        ),
        array(
          'first_name'=> 'Jan',
          'last_name'=> 'Elwes',
          'name'=> 'Jan Elwes',
          'mobile'=> '9172832182'
        ),
        array(
          'first_name'=> 'Carolyn',
          'last_name'=> 'Brooke',
          'name'=> 'Carolyn Brooke',
          'mobile'=> '9298938129'
        ),
        array(
          'first_name'=> 'Elise',
          'last_name'=> 'Ogden',
          'name'=> 'Elise Ogden',
          'mobile'=> '9223122399'
        ),
        array(
          'first_name'=> 'Rhetta',
          'last_name'=> 'Bone',
          'name'=> 'Rhetta Bone',
          'mobile'=> '0909213123'
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