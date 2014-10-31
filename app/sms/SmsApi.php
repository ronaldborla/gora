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
     * Set user
     */
    private $user;

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

      // Set user
      $this->user = User::findByMobile($mobile);
      // If not found
      if (!$this->user || !$this->user->id) {
        // New user
        $password = Str::quickRandom(6);
        // Create
        $this->user = User::createUser(array(
          'mobile'=> $mobile,
          'name'=> Chikka::cleanupMobileNumber($mobile),
          'password'=> $password,
          'location'=> '',
          'bounds'=> ''
        ));
        // Welcome user
        if (!$this->test) {
          static::welcomeUser($this->sender, $this->user, $password);
        }
      }

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
     * Welcome user
     */
    static function welcomeUser(SmsSenderInterface $sender, User $user, $password = '') {

      // Message
      $message = 'Welcome ' . $user->name . ' to Gora' . PHP_EOL . PHP_EOL .
                 'You may login with your mobile number to our website at ' . action('UsersController@login') . PHP_EOL . 
                 ( $password ? ('Your temporary password is ' . $password . PHP_EOL) : '' ) . PHP_EOL .
                 'Gora Team.';
      // Send message
      return $sender->send($user->mobile, $message);
    }

  }