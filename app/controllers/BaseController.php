<?php

class BaseController extends Controller {

	// Set user
	protected $user = null;
	// Set chikka 
	protected $sms = null;

	/**
	 * Construct
	 */
	function __construct() {
		// Declare sms sender
		$this->sms = new SmsSender(Config::get('sms.chikka'));

		// Share
		View::share(array(
			// As current user
			'currentUser'=> $this->user
		));
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}
