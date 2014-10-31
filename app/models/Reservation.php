<?php

  /**
   * Reservation
   */

  class Reservation extends Eloquent {

    // Set table
    protected $table = 'reservations';

    // Fillables
    protected $fillable = array('id', 'members', 'status', 'establishment_id', 'user_id');

    const PENDING = 0;
    const PROCESSED = 1;
    const CANCELLING = 2;
    const CANCELLED = 3;
    const DELETED = 4;

    /**
     * Make a reservation
     */
    static function make(Establishment $establishment, User $user, $members = array()) {

      // If there are no members or user is not included
      if (!$members || !in_array($user->id, $members)) {
        // Add user
        $members[] = $user->id;
      }
      // Create and return
      $reservation = new static(array(
        'members'=> json_encode($members),
        'status'=> static::PENDING,
        'establishment_id'=> $establishment->id,
        'user_id'=> $user->id
      ));
      // save
      $reservation->save();
      // Fire an event
      Event::fire('reservation.make', array($reservation));
      // Return reservation
      return $reservation;
    }

    /**
     * Get establishment
     */
    function establishment() {
      // Return
      return Establishment::where('id', '=', $this->establishment_id)->first();
    }

    /**
     * Get user
     */
    function user() {
      // Return
      return User::where('id', '=', $this->user_id)->first();
    }

    /**
     * Get members
     */
    function members() {

      // Return
      return User::whereIn('id', json_decode($this->members, true))->get();
    }

  }