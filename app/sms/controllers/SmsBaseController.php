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
     * Set args
     */
    protected $args;

    /**
     * Our response
     */
    protected $response;


    /**
     * Constructor
     */
    function __construct(SmsApiInterface $api, $args = '') {
      // Set
      $this->api = $api;
      // Set args
      $this->args = $args;
    }

    /**
     * Get
     */
    function __get($name) {
      // If name is keyword
      if ($name == 'keyword') {
        // Get class
        $class = get_called_class();
        // Extract name
        $keyword = strtolower(substr($class, 3, strlen($class) - 13));
        // Return
        return $this->api->keywordIsReserved($keyword) ? null : $keyword;
      }
    }

    /**
     * Instance
     */
    static function instance(SmsApiInterface $api, $args = '') {
      // Just declare
      return new static($api, $args);
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