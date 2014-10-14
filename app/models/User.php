<?php

	/**
	 * User model
	 */

	class User extends Eloquent {

		// User table
		protected $table = 'user';

		// Fillable
		protected $fillable = array('privilege', 'status');

		// Current user
		private static $current = null;

		// Set session
		private $session = null;

		/**
		 * Privileges
		 */
		const MEMBER 		= 1;
		const CLIENT 		= 2;
		const MODERATOR = 4;
		const ADMIN 		= 8;

		/**
		 * Status
		 */
		const INACTIVE = 0;
		const ACTIVE 	 = 1;
		const FLAGGED  = 2;
		const BANNED 	 = 3;
		const DELETED  = 4;

		/**
		 * Check for user
		 */
		static function check() {
			// If there's no current user
			if (static::$current === null) {
				// Decode a session
				$session = UserSession::decode();
				// If there's no session
				if (!$session) {
					// Create new session
					$session = new UserSession();
					// Create new user
					$user = new User();
					// Set session
					$user->setSession($session);
					// Set current user
					static::$current = $user;
				} else {
					// Set user session as this session
					$session->user->setSession($session);
					// Set current user
					static::$current = $session->user;
				}
			}
			// Return active
			return static::$current->session()->active();
		}

		/**
		 * Get current user
		 */
		static function current() {
			// If there's no current
			if (static::$current === null) {
				// Check
				static::check();
			}
			// Return
			return static::$current;
		}

		/**
		 * Authenticate a user
		 */
		static function authenticate($auth, $password, $useCookie = false) {
			// Find by auth
			$user = static::findByAuth($auth);
			// If there's user
			if ($user) {
				// Verify password
				if ($user->verifyPassword($password)) {
					// Login user
					$user->login($useCookie);
				}
			}
			// Return
			return (static::$current = $user);
		}

		/**
		 * Login a user
		 */
		function login($useCookie = false) {
			// Just activate a new session
			$session = UserSession::activate($this, $useCookie);
			// Set session and return
			return $this->setSession($session);
		}

		/**
		 * Logout a user
		 */
		function logout() {
			// If there's session
			if ($this->session()) {
				// Deactivate session
				$this->session()->deactivate();
			}
			// Return
			return $this;
		}

		/**
		 * Find user by auth
		 */
		static function findByAuth($auth, $type = false) {
			// Create user auth
			$userAuth = new UserAuth();
			// Check if there's type
			if ($type !== false) {
				// Set condition
				$userAuth = $userAuth->where('type', '=', $type);
			}
			// Set value and get first
			$userAuth = $userAuth->where('value', '=', $auth)->limit(1)->first();
			// Return user
			return $userAuth ? $userAuth->user : null;
		}

		/**
		 * Change password
		 */
		function changePassword($password) {
			// Create new secret
			$this->secret = str_random(10);
			// Set password
			$this->password = Hash::make($password);
			// Return
			$this->save();
			// Return 
			return $this;
		}

		/**
		 * Verify password
		 */
		function verifyPassword($password) {
			// Return
			return Hash::check($password, $this->password);
		}

		/**
		 * Register new user
		 */
		static function register($auth, $fields = array()) {
			// If auth is not array, consider it as mobile
			if (!is_array($auth)) {
				$auth = array(
					// Set as mobile
					UserAuth::MOBILE => $auth
				);
			}
			// Set default fields
			$fields += array(
				'privilege'	=> static::MEMBER,
				'status'		=> static::INACTIVE
			);
			// Create user
			$user = new User($fields);
			// Save user
			$user->save();
			// Loop through auth
			foreach ($auth as $type=> $value) {
				// If not valid type, skip
				if (UserAuth::types($type) === false) continue;
				// Create auth
				$userAuth = new UserAuth(array(
					'type'=> $type,
					'value'=> $value,
					'user_id'=> $user->id
				));
				// Save
				$userAuth->save();
			}
			// Return user
			return $user;
		}

		/**
		 * Set session
		 */
		function setSession(UserSession $session) {
			// Set as session
			$this->session = $session;
			// Return
			return $this;
		}

		/**
		 * Get session
		 */
		function session() {
			// Return
			return $this->session;
		}

	}