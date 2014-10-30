<?php

  /**
   * More Controller
   */

  class SmsKeywordMore extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      // Return
      return $this->getNavigation('more');
    }
  }