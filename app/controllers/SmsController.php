<?php

  /**
   * SMS Controller
   */

  class SmsController extends BaseController {

    /**
     * Check if test
     */
    function isTest() {
      // Check if test is set
      return Input::get('test') == '1';
    }

    /**
     * Receiver
     */
    function receiver() {

      // Return
      $return = array('success'=> false, 'message'=> 'Invalid request');

      // Receive
      if ($data = $this->sms->receive()) {
        // Instantiate smsApi
        $smsApi = new SmsApi($this->sms);
        // Call SmsApi
        return $smsApi->receive($data) // Set received data
                      ->test($this->isTest()) // Set if test
                      ->request(); // Send request
      }

      // Since there's no valid request, redirect to homepage
      return $this->isTest() ? print_r($return,true) : Response::json($return);
    }

    /**
     * Test
     */
    function test() {

      // Set response
      $response = null;
      // If there's post
      if (Request::isMethod('post')) {
        // Setup post
        $post = array(
          'message_type'=> 'incoming',
          'mobile_number'=> Input::get('mobile'),
          'shortcode'=> '29290001',
          'request_id'=> sha1(microtime(true)),
          'message'=> Input::get('message'),
          'timestamp'=> time(),
          'test'=> 1,
        );
        // Do post
        $response = Chikka::post(URL::action('SmsController@receiver'), $post);
      }
?>
<html>
<head>
<title>Test SMS</title>
  <style type="text/css">
  input,textarea,button { font: 14px Arial; border: 1px #ccc solid; border-radius: 3px; padding: 4px 6px 4px 6px; } 
  button { cursor: pointer; }
</style>
</head>
<body>
  <?php
  // If there's response
  if ($response) {
    // Print response
    echo '<pre>',$response,'</pre>';
  }
  // Create form
  echo Form::open(array('action'=> 'SmsController@test', 'method'=> 'post'));

  echo Form::text('mobile', Request::get('mobile'), array('placeholder'=> 'Mobile')),'<br />';
  echo Form::textarea('message', Request::get('message'), array('placeholder'=> 'Message')),'<br />';
  echo Form::button('Send', array('type'=> 'submit'));

  echo Form::close();
  ?>
</body>
</html>
<?php
    }
  }