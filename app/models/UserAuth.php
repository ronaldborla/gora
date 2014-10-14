<?php

  /**
   * User Auth
   */

  class UserAuth extends Eloquent {

    // User auth table
    protected $table = 'user_auth';

    // Fillable
    protected $fillable = array('type', 'value', 'user_id');

    /**
     * User auth types
     */
    const MOBILE = 1;

    /**
     * Auth types
     */
    static function types($type = false) {
      // Types
      $types = array(
        // Mobile number
        static::MOBILE => 'mobile'
      );
      // If there's type
      if ($type !== false) {
        // Return
        return isset($types[$type]) ? $types[$type] : false;
      }
      // Return all
      return $types;
    }

    /**
     * Get user
     */
    function user() {
      // Belongs to user
      return $this->belongsTo('User');
    }

  }