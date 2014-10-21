<?php

  /**
   * Gora keyword controller
   */

  class SmsKeywordGora extends SmsKeywordController {

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