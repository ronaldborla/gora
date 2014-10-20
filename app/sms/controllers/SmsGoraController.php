<?php

  /**
   * Gora keyword controller
   */

  class SmsGoraController extends SmsBaseController {

    /**
     * Initialize
     */
    function initialize() {

      // Here we create our response
      $this->response = 'gorabels! ' . $this->args . ', keyword: ' . $this->keyword;

      // Return self
      return $this;
    }
  }