<?php

  /**
   * Location controller
   */

  class SmsKeywordLocation extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      // If there's no args, return location
      if (!trim($this->args)) {

        $this->response = (($this->api->user->location) ? ('Your current location is ' . $this->api->user->location) : 'You have not yet set your location') . 
                          '. To change your location, reply with "location <your address>". Example, "location metro manila".';
        // Return
        return $this;
      }

      // Locate address
      $locate = Maps::locate($this->args);
      // Limit
      $limit = 5;

      // If there are any
      if ($locate) {
        // Count
        $count = count($locate);
        // If there are a lot of locations
        if ($count > 1) {

          $message = array('We found multiple locations matching your address', '');
          // Limit to 26
          $locate = array_slice($locate, 0, $limit);

          // Set options
          $options = array();

          foreach ($locate as $i=> $loc) {
            // Set letter
            $letter = chr($i + 65);
            // Add to message
            $message[] = $letter . '. ' . $loc->address;
            // Add to options
            $options[$letter] = $loc->toArray();
          }
          $message[] = '';
          $message[] = 'Reply with letter of correct address (e.g. "A")' . (
            ($count > $limit) ? (PHP_EOL . 'We recommend you narrow down the address.') : '.'
          );
          // Set response
          $this->response = implode(PHP_EOL, $message);

          // Subscribe
          $this->api->user->session('select')->subscribe(array(
            'action'=> 'location',
            'options'=> $options
          ));

        } else {
          // Set location
          $this->setLocation($locate[0]);
        }
      } else {
        // Couldn't locate
        $this->response = 'We could not locate the specified address.';
      }

      return $this;
    }
  }