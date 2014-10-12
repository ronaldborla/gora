<?php

  /**
   * User seeder
   */

  class UserSeeder extends Seeder {

    /**
     * Run
     */
    public function run() {

      // Truncating
      $this->command->info('Truncating user tables..');
      // Truncate 
      DB::table('user')->truncate();
      DB::table('user_auth')->truncate();
      DB::table('user_session')->truncate();
      // Truncated
      $this->command->info('User tables truncated!');

      // Set users
      $users = array(
        '9087800765'=> array(
          'status'=> User::ACTIVE, 
          'privilege'=> User::MEMBER + User::MODERATOR + User::ADMIN
        ),
        '9425569939'=> array(
          'status'=> User::ACTIVE, 
          'privilege'=> User::MEMBER
        ),
        '9223682399'=> array(
          'status'=> User::ACTIVE, 
          'privilege'=> User::MEMBER + User::CLIENT
        )
      );

      // Creating
      $this->command->info('Creating users..');
      // Seed users
      foreach ($users as $mobile=> $fields) {
        // Register user
        $user = User::register($mobile, $fields);
        // Change password
        $user->changePassword('password');
      }
      // Created
      $this->command->info('Users created!');

    }
  }