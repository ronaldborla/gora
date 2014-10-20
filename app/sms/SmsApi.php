<?php

  /**
   * SMS API
   */

  class SmsApi implements SmsApiInterface {

    /**
     * Sender
     */
    private $sender;
    /**
     * Message id
     */
    private $id;
    /**
     * Mobile
     */
    private $mobile;
    /**
     * Message
     */
    private $message;

    /**
     * Test only
     */
    private $test = false;

    /**
     * Constructor
     */
    function __construct(SmsSenderInterface $smsSender = null) {

      // Set sender
      if ($smsSender) {
        // Set it
        $this->setSender($smsSender);
      }
    }

    /**
     * Get
     */
    function __get($name) {

      // If set
      if (property_exists(get_called_class(), $name)) {
        // Return
        return $this->$name;
      }
    }

    /**
     * Instantiate
     */
    static function instance() {
      // Return
      return new static();
    }

    /**
     * Set SMS Sender
     */
    function setSender(SmsSenderInterface $smsSender) {
      // Set sender
      $this->sender = $smsSender;
      // Return this
      return $this;
    }

    /**
     * Set id
     */
    function setId($id) {
      // Set id
      $this->id = $id;
      // Return
      return $this;
    }
    /**
     * Set mobile
     */
    function setMobile($mobile) {
      // Set mobile
      $this->mobile = $mobile;
      // Return
      return $this;
    }
    /**
     * Set message
     */
    function setMessage($message) {
      // Set message
      $this->message = $message;
      // Return
      return $this;
    }

    /**
     * Receive
     */
    function receive($data) {
      // Just set everything
      return $this->setId($data['id'])
                  ->setMobile($data['mobile'])
                  ->setMessage($data['message']);
    }

    /**
     * Check if keyword is reserved
     */
    function keywordIsReserved($keyword) {
      // Return
      return in_array($keyword, array(
        'base',
        'general'
      ));
    }

    /**
     * Request
     */
    function request() {
      /**
       * Parse
       */
      $parse = $this->parse();
      // Set controller
      $controllerName = null;

      // Check if there's keyword, and it's not reserved
      if ($parse['keyword'] && !$this->keywordIsReserved($parse['keyword'])) {
        // If controller exists
        if (class_exists('Sms' . ($keyword = ucfirst($parse['keyword'])) . 'Controller')) {
          // Set as controller
          $controllerName = $keyword;
        }
      }

      // If there's no name
      if (!$controllerName) {
        // Set controller name as general
        $controllerName = 'General';
        // Set args as the whole message
        $parse['args'] = $this->message;
      }

      // Declare controller
      $controller = 'Sms' . $controllerName . 'Controller';
      // Return
      return $controller::instance($this, $parse['args'])
                        // Send back request
                        ->request();
    }

    /**
     * Test only
     */
    function test($isTest = true) {
      // Set
      $this->test = $isTest;
      // Return
      return $this;
    }

    /**
     * Parse message
     */
    function parse($message = false) {
      // Get message
      $message = ($message === false) ? $this->message : $message;
      // Split by space
      $arrMessage = explode(' ', $message);

      // Get keyword
      $keyword = isset($arrMessage[0]) ? $arrMessage[0] : '';
      // Get args
      $args = isset($arrMessage[1]) ? implode(' ', array_slice($arrMessage, 1)) : '';

      // Return keyword and args
      return array(
        'keyword'=> strtolower($keyword),
        'args'=> $args
      );
    }
  }