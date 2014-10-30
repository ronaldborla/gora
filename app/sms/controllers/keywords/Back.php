<?php

  /**
   * Back
   */

  class SmsKeywordBack extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      // Return
      return $this->getNavigation('back');
    }
  }