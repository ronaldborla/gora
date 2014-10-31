<?php

  /**
   * User
   */

  class User extends \Cartalyst\Sentry\Users\Eloquent\User {

    /**
     * Login attribute
     */
    protected static $loginAttribute = 'mobile';
    
    /**
     * Find user
     */
    static function findByMobile($mobile) {
      // Try
      try {
        // Find by login
        $user = Sentry::findUserByLogin(static::shortenMobile($mobile));
        // Return
        return $user;
        // If not found
      } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
        // Return nothing
        return null;
      }
    }

    /**
     * Create
     */
    static function createUser($user) {
      // Set activated
      $user['activated'] = true;

      if (isset($user['mobile'])) {
        // Shorten
        $user['mobile'] = static::shortenMobile($user['mobile']);
      }

      // Create user
      $createUser = Sentry::createUser($user);
      // Use member
      $adminGroup = Sentry::findGroupById(3);
      // Add to members
      $createUser->addGroup($adminGroup);

      // Return
      return $createUser;
    }

    /**
     * Shortened mobile
     */
    static function shortenMobile($mobile) {
      // Return
      return Chikka::getLast10Digits($mobile);
    }

    /**
     * Get bounds
     */
    function bounds() {
      // Return json
      return $this->bounds ? new Bounds($this->bounds) : false;
    }

    /**
     * Get center
     */
    function center() {
      // Return
      return $this->bounds ? Maps::boundsCenter($this->bounds()) : false;
    }

    /**
     * Get radius
     */
    function radius() {
      // Return
      return $this->bounds ? Maps::boundsRadius($this->bounds()) : false;
    }

    /**
     * Get session
     */
    function session($title) {
      // Return subscription
      return UserSession::getSubscription($title, $this);
    }

  }