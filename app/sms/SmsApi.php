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
     * Request
     */
    function request() {
      // Return
      return SmsKeywordController::instance($this)->request();
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

    /**
     * Get filters
     */
    function filters($message, $fields) {
      // Locate all fields in message
      $filterPos = array();
      // If there are fields
      if (is_array($fields) && $fields) {
        // Loop through fields
        foreach ($fields as $field) {
          // Find this field
          
        }
      }
    }
  }