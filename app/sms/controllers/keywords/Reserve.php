<?php

  /**
   * Reserve Controller
   */

  class SmsKeywordReserve extends SmsKeywordController {

    /**
     * Do reserve
     */
    function initialize() {

      // Check for single
      $single = $this->api->user->session('single');
      // If active
      if ($single->active()) {

        // Get options
        $options = $single->options();
        // If there's id
        if (isset($options['id'])) {
          // Load establishment
          $establishment = Establishment::where('id', '=', intval($options['id']))->first();
          // If there's any
          if ($establishment && $establishment->id) {

            // Make a reservation
            $establishment->makeReservation($this->api->user);
            // Set response
            $this->response = 'You have made a reservation to ' . $establishment->name . '. Please wait for a confirmation.';

          }

        }

      } else {

        // No active establishment
        $this->response = 'You have no active establishment selected.';

      }
      // Return
      return $this;
    }
  }