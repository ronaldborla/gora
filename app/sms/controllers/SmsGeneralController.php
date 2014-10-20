<?php

  /**
   * General request
   */

  class SmsGeneralController extends SmsBaseController {

    /**
     * Process
     */
    function initialize() {

      $this->response = 'Naa kay ginapangita? kani noh: ' . $this->args;

      return $this;
    }
  }