<?php

  /**
   * Controller Interface
   */

  interface SmsControllerInterface {

    /**
     * Initialize
     */
    function initialize();
    /**
     * Request is required
     */
    function request();
  }