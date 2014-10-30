<?php

  /**
   * Subscription
   */

  class Subscription extends Eloquent {

    /**
     * Table
     */
    protected $table = 'subscriptions';
    // Fillables
    protected $fillables = array('id', 'type', 'options', 'status', 'establishment_id', 'user_id');

    /**
     * Types of subscription
     */
    const ESTABLISHMENT = 1;
    const KEYWORD       = 2;
    /**
     * Status
     */
    const ACTIVE    = 0;
    const CANCELLED = 1;

    /**
     * Make a subscription
     */
    static function make($type, User $user, $options = '', Establishment $establishment = null) {

      // Set subscription
      $subscription = null;

      // Check if subscription already exists
      if (($type == static::ESTABLISHMENT) && $establishment && $establishment->id) {
        // Check
        $subscription = static::where('type', '=', static::ESTABLISHMENT)
                              ->where('establishment_id', '=', $establishment->id)
                              ->where('user_id', '=', $user->id)
                              ->first();
      }
      // If keyword
      if (($type == static::KEYWORD) && ($options = Str::lower(trim($options)))) {
        // Check
        $subscription = static::where('type', '=', static:KEYWORD)
                              ->where('options', '=', $options)
                              ->where('user_id', '=', $user->id)
                              ->first();
      }
      // If found
      if ($subscription && $subscription->id) {
        // Check if not active
        if ($subscription != static::ACTIVE) {
          // Set to active
          $subscription->status = static::ACTIVE;
          // Save
          $subscription->save();
        }
        // Return
        return $subscription;
      }

      // Create
      $subscription = new static(array(
        'type'=> $type,
        'options'=> Str::lower($options),
        'status'=> static::ACTIVE,
        'establishment_id'=> $establishment ? $establishment->id : 0,
        'user_id'=> $user->id
      ));
      // Save
      $subscription->save();
      // Fire
      Event::fire('subscription.make', array($subscription));
      // Return
      return $subscription;
    }

  }