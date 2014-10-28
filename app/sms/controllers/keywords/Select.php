<?php

  /**
   * Select option
   */

  class SmsKeywordSelect extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      // Check if user is subscribed to select
      $select = $this->api->user->session('select');
      // If active
      if ($select->active()) {

        // Get options
        $options = $select->options();
        // Action
        $action = isset($options['action']) ? $options['action'] : null;
        // Get selected
        $selected = null;

        // Capital first
        $letter = strtoupper($this->args);
        // If set
        if (isset($options['options'][$letter])) {
          // Set selected
          $selected = $options['options'][$letter];
        } else {
          // Lowercase
          $letter = strtolower($this->args);
          // Set selected
          if (isset($options['options'][$letter])) {
            // Set
            $selected = $options['options'][$letter];
          }
        }

        // If there's any selected
        if ($selected) {

          // Switch
          switch ($action) {

            case 'location':
              // Set location
              $this->setLocation(new Location($selected));
              break;

            case 'single':
              // Get establishment
              $establishment = Establishment::where('id', '=', intval($selected))->first();
              // If there's any
              if ($establishment && $establishment->id) {
                // Pull single
                $this->pullSingle($establishment);
              }
              break;

          }

          $select->unsubscribe();

        } else {

          // Invalid choice
          $this->response = 'Invalid choice. Please try again.';

        }

      } else {
        // Set response as invalid
        $this->response = 'You don\'t have any available selections.';
      }

      // Return
      return $this;
    }
  }