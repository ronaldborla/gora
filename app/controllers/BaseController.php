<?php

class BaseController extends Controller {

	// Set user
	protected $user = null;

	/**
	 * Construct
	 */
	function __construct() {
		// Set current user
		$this->user = User::check() ? User::current() : null;
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
