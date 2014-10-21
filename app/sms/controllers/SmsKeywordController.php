<?php

  /**
   * General request
   */

  class SmsKeywordController extends SmsBaseController {

    /**
     * Args
     */
    protected $args;

    /**
     * Get
     */
    function __get($name) {
      // Get class
      $class = get_called_class();
      // If name is keyword
      if ($class != 'SmsKeywordController' && $name == 'keyword') {
        // Extract name
        return strtolower(substr($class, 10));
      }
    }

    /**
     * Process
     */
    function initialize() {

      // If there's already a keyword, return
      if ($this->keyword) {
        // Return
        return $this;
      }
      /**
       * Parse message
       */
      $parse = $this->api->parse();

      if (!$this->keywordExists($parse['keyword'])) {
        // Set keyword as search
        $parse['keyword'] = 'Search';
        // Args as whole message
        $parse['args'] = $this->api->message;
      }

      // call and return
      return $this->call($parse['keyword'], $parse['args']);
    }

    /**
     * Call keyword controller
     */
    function call($keyword, $args = '') {
      // Ucfirst
      $keyword = ucfirst($keyword);
      // Set controller
      $controller = 'SmsKeyword' . $keyword;
      // Require
      require_once($this->keywordsDir() . $keyword . '.php');

      // Initialize controller
      $this->response = $controller::instance($this->api)
                                   ->setArgs($args)
                                   ->initialize()
                                   ->response();
      // Return
      return $this;
    }

    /**
     * Alias to
     */
    function alias($keyword) {
      // Return
      $this->call($keyword, $this->args);
      // Return
      return $this;
    }

    /**
     * Get keywords dir
     */
    function keywordsDir() {
      // Return
      return dirname(__FILE__) . '/keywords/';
    }

    /**
     * Check if keyword is valid
     */
    function keywordExists($keyword) {
      // Return
      return is_file($this->keywordsDir() . ucfirst($keyword) . '.php');
    }

    /**
     * Set args
     */
    function setArgs($args) {
      // Set
      $this->args = $args;
      // return
      return $this;
    }
  }