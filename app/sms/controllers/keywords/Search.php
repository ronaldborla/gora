<?php

  /**
   * Search
   */

  class SmsKeywordSearch extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      $this->response = 'Are you looking for this? ' . $this->args;
      // return
      return $this;
    }
  }