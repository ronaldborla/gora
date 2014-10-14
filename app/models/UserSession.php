<?php

  /**
   * User session
   */

  class UserSession extends Eloquent {

    // User table
    protected $table = 'user_session';

    // Fillable
    protected $fillable = array('ip', 'expires', 'status', 'user_id');

    // Session/Cookie name
    const NAME = 'user';
    // Expires
    const COOKIE = 2592000;
    const SESSION = 1800;

    /**
     * Session status
     */
    const INACTIVE  = 0;
    const ACTIVE    = 1;

    // Set if expired
    private $expired = false;

    /**
     * Check if expired
     */
    function expired() {
      // Return
      return $this->expired;
    }

    /**
     * Check if session is active
     */
    function active() {
      // If inactive
      if ($this->status == static::INACTIVE) {
        // Return false
        return false;
      }
      // Check date
      if ($this->expired()) {
        // Set as inactive
        $this->status = static::INACTIVE;
        // Save
        $this->save();
        // Return false
        return false;
      }
      // Return true
      return true;
    }

    /**
     * Reactivate
     */
    function reactivate() {
      // Just save
      $this->save();
      // Return
      return $this;
    }

    /**
     * Activate a session for a user
     */
    static function activate(User $user, $useCookie = false) {
      // If there's no user
      if (!$user->id) {
        // Exit immediately
        return null;
      }
      // Create new session
      $session = new static(array(
        // Set client ip
        'ip'=> Request::ip(),
        // 30 days if to use cookie, else 30 minutes
        'expires'=> $useCookie ? static::COOKIE : static::SESSION,
        // Set status
        'status'=> static::ACTIVE,
        // Set user id
        'user_id'=> $user->id
      ));
      // Save
      $session->save();
      // Set key
      $key = $session->encode();

      // Create session
      if ($useCookie) {
        // Set in cookie
        Cookie::make(static::NAME, $key, static::COOKIE / 60);
      } else {
        // Set in session
        Session::put(static::NAME, $key);
      }

      // Return session
      return $session;
    }

    /**
     * Clear session
     */
    function clear() {
      // Forget session and cookie
      Cookie::forget(static::NAME);
      Session::forget(static::NAME);
      // Return
      return $this;
    }

    /**
     * Deactivate
     */
    function deactivate() {
      // Set status
      $this->status = static::INACTIVE;
      // Save
      $this->save();
      // Clear session
      $this->clear();
      // Return
      return $this;
    }

    /**
     * Get user
     */
    function user() {
      // Belongs to user
      return $this->belongsTo('User');
    }

    /**
     * Encode key
     */
    function encodeKey() {
      // Session and key
      $sessionKey = array(
        'session'=> $this->id,
        'key'=> $this->user->secret
      );
      // Return
      return json_encode($sessionKey);
    }

    /**
     * Encode session
     */
    function encode() {
      // Return key
      return $this->id . ':' . Hash::make($this->encodeKey());
    }

    /**
     * Verify
     */
    function verify() {
      // If expired
      if (time() > (strtotime($this->updated_at) + $this->expires)) {
        // Set expired
        $this->expired = true;
      }
      // Return
      return $this;
    }

    /**
     * Decode session
     */
    static function decode() {
      // Get key
      $key = Cookie::get(static::NAME);
      // If there's no key
      if (!$key) {
        // Get from session
        $key = Session::get(static::NAME);
        // If there's still no key, exit
        if (!$key) return null;
      }
      // Split by :
      $arrKey = explode(':', $key);
      // If there's no second key
      if (!isset($arrKey[1]) || !($arrKey[1] = trim($arrKey[1]))) {
        // Exit
        return null;
      }
      // Load session
      $session = static::find(intval($arrKey[0]));

      // Check hash
      if (!$session || !$session->id || !Hash::check($session->encodeKey(), $arrKey[1])) {
        // Exit
        return null;
      }
      // Verify and check if active
      if ($session->verify()->active()) {
        // Just reactivate
        $session->reactivate();
      }
      // Return session
      return $session;
    }

  }