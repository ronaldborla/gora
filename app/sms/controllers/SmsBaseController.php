<?php

  /**
   * Base controller
   */

  class SmsBaseController implements SmsControllerInterface {

    /**
     * Set api
     */
    protected $api;

    /**
     * Our response
     */
    protected $response;


    /**
     * Constructor
     */
    function __construct(SmsApiInterface $api) {
      // Set
      $this->api = $api;
    }

    /**
     * Instance
     */
    static function instance(SmsApiInterface $api) {
      // Just declare
      return new static($api);
    }

    /**
     * Initialize
     */
    function initialize() {
      // Return
      return $this;
    }

    /**
     * Send response
     */
    function response() {
      // If test
      if ($this->api->test) {
        // Return response
        return $this->response;
      } else {
        // Return accept
        return $this->api->sender->accept();
      }
    }

    /**
     * Request
     */
    function request() {
      // Initialize first
      return $this->initialize()->response();
    }

  }