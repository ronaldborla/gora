<?php

  /**
   * Contact model
   */

  class Contact extends Eloquent {

    // User table
    protected $table = 'contact';

    // Fillable
    protected $fillable = array('type', 'value', 'description', 'order', 'primary', 'establishment_id');

    /**
     * Contact types
     */
    const MOBILE    = 1;
    const TELEPHONE = 2;
    const EMAIL     = 3;

    /**
     * Get types
     */
    static function getTypes($type = false) {
      // Set types
      $types = array(
        static::MOBILE    => 'mobile',
        static::TELEPHONE => 'telephone',
        static::EMAIL     => 'email'
      );
      // If set
      if ($type !== false) {
        // Return
        return isset($types[$type]) ? $types[$type] : false;
      }
      // Return all
      return $types;
    }

  }