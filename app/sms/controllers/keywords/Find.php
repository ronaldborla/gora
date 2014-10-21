<?php

  /**
   * Find
   */

  class SmsKeywordFind extends SmsKeywordController {

    /**
     * On initialize
     */
    function initialize() {

      // Return as alias
      return $this->alias('search');
    }
  }