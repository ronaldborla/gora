<?php

  /**
   * SmsSender
   */

  class SmsSender implements SmsSenderInterface {

    // Our sender
    private $sender;

    /**
     * Constructor
     */
    function __construct($config) {
      // Chikka is our sender
      $this->sender = new Chikka($config);
    }

    /**
     * Send message
     */
    function send($mobile, $message) {
      // Send message
      $this->sender->send($mobile, $message);
      // Return this sender
      return $this;
    }

    /**
     * Send reply
     */
    function reply($id, $mobile, $message, $cost = 0) {
      // Send reply
      $this->sender->reply($id, $mobile, $message, $cost);
      // Return this sender
      return $this;
    }

    /**
     * Receive message
     */
    function receive($callback = null) {
      // Receive message first
      $receive = $this->sender->receiveMessage();
      // If false, return nothing
      if ($receive === false) return array();
      // Set args
      $args = array(
        'id'=> $receive->request_id,
        'mobile'=> $receive->mobile_number,
        'message'=> $receive->message
      );
      // If there's callback
      if (is_callable($callback)) {
        // Call method
        call_user_func_array($callback, array(
          $args['id'],
          $args['mobile'],
          $args['message']
        ));
      }
      // Return all
      return $args;
    }

    /**
     * Do chikka accept
     */
    function accept() {
      // Return
      return $this->sender->accept();
    }
    /**
     * Error
     */
    function error() {
      // Return
      return $this->sender->error();
    }
  }