<?php

  /**
   * Sessions
   */

  class UserSession extends Eloquent {

    /**
     * Session table
     */
    protected $table = 'user_sessions';

    /**
     * Status
     */
    const INACTIVE  = 0;
    const ACTIVE    = 1;
    const EXPIRED   = 2;

    /**
     * Set fillable
     */
    protected $fillable = array('id', 'title', 'options', 'expires', 'status', 'user_id');

    /**
     * Get subscription session
     */
    static function getSubscription($title, User $user) {
      // Find subscription for user
      $session = static::where('title', '=', $title)
                       ->where('user_id', '=', $user->id)
                       ->first();
      // If there's nothing
      if (!$session || !$session->id) {
        // Create new
        $session = new static(array(
          'title'=> $title,
          'options'=> '',
          'expires'=> 0,
          'status'=> static::INACTIVE,
          'user_id'=> $user->id
        ));
        // Save
        $session->save();
      } else {
        // If there's expiration
        if ($session->expires && ($session->status != static::EXPIRED)) {
          // Updated
          $updated = strtotime($session->updated_at);
          // Check if expired
          if (time() > ($updated + $session->expires)) {
            // Then it's expired
            $session->status = static::EXPIRED;
            // Save
            $session->save();
          }
        }
      }
      // Return
      return $session;
    }

    /**
     * Check if active
     */
    function active() {
      // Return
      return ($this->status == static::ACTIVE);
    }

    /**
     * Get options
     */
    function options() {
      // Return
      return @json_decode($this->options, true);
    }

    /**
     * Subscribe
     */
    function subscribe($options, $expires = 300) {
      // Update
      $this->options = is_array($options) ? json_encode($options) : $options;
      $this->expires = $expires;
      $this->status = static::ACTIVE;
      // Save
      $this->save();
      // Return
      return $this;
    }

    /**
     * Unsubscribe
     */
    function unsubscribe() {
      // Set inactive
      $this->status = static::INACTIVE;
      // Save
      $this->save();
      // Return
      return $this;
    }
    
  }