<?php

  /**
   * Gora keyword controller
   */

  class SmsKeywordGora extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      // Get by gora
      $establishment = Establishment::getGora($this->api->user);

      // If there's any
      if ($establishment && $establishment[0]->id) {
        // Pull single
        $this->pullSingle($establishment[0]);
        // Add message
        $this->response = rtrim($this->response, '. ') . "\n";
      } else {
        // Set response
        $this->response = 'We haven\'t found any establishment within your current location. ';
      }
      // Add to response
      $this->response .= 'Note that you can always change your location by typing "location".';

      // Return self
      return $this;
    }
  }