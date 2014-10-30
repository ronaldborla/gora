<?php

  /**
   * Reservation observer
   */

  class ReservationObserver {

    /**
     * Reservation made
     */
    function make(Reservation $reservation) {

    }

    /**
     * Subscribe to events
     */
    function subscribe($events) {

      // Made reservations
      $events->listen('reservation.make', 'ReservationObserver@make');

    }
  }