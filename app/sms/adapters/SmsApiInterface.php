<?php

  /**
   * Api Interface
   */

  interface SmsApiInterface {

    /**
     * Set sms sender
     */
    function setSender(SmsSenderInterface $smsSender);
    /**
     * Set message id
     */
    function setId($id);
    /**
     * Set mobile
     */
    function setMobile($mobile);
    /**
     * Set message
     */
    function setMessage($message);
    /**
     * Receive
     */
    function receive($data);
    /**
     * Request
     */
    function request();

  }