<?php

  /**
   * Sender Interface
   */

  interface SmsSenderInterface {

    /**
     * Send a message to a mobile
     */
    function send($mobile, $message);
    /**
     * Reply to a message
     */
    function reply($id, $mobile, $message, $cost = 0);
    /**
     * Receive sms
     * @param callback $callback Callback that passed 3 parameters: $id, $mobile, $message
     * @return array Array that contains the 3 items: id, mobile, message
     */
    function receive($callback = null);

  }