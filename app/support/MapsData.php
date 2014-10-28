<?php

  /**
   * MapsData
   */

  class MapsData {

    /**
     * Data
     */
    protected $data = array();

    // Fields
    protected static $fields = array();

    /**
     * Construct
     */
    function __construct($data = array()) {

      // Load
      $this->load($data);
    }

    /**
     * Load
     */
    function load($data) {
      // If string, decode
      if (is_string($data)) {
        // Decode
        $data = @json_decode($data, true);
      }
      // If still not array, set empty
      if (!is_array($data)) {
        // Set empty
        $data = array();
      }
      // Set from fields
      $this->setFromFields($data);
      // Return
      return $this;
    }

    /**
     * Get
     */
    function __get($name) {

      // If requested
      if (in_array($name, static::$fields)) {
        // If not set
        if (!isset($this->data[$name])) {
          // Callback
          $callback = 'set' . ucfirst($name);
          // Set empty
          $this->$callback('');
        }
        // Return
        return $this->data[$name];
      }
    }

    /**
     * Call
     */
    function __call($function, $args) {
      // Set it
      if (substr($function, 0, 3) == 'set') {
        // Get after
        $name = strtolower(substr($function, 3));
        // If name is set
        if (in_array($name, static::$fields) && isset($args[0])) {
          // Set it
          $this->data[$name] = floatval($args[0]);
        }
      }
    }

    /**
     * To string
     */
    function __toString() {
      // Return json
      return @json_encode($this->toArray());
    }

    /**
     * To array
     */
    function toArray() {
      // Set array
      $array = array();
      // Set each
      foreach (static::$fields as $field) {
        // Set array
        $array[$field] = $this->$field;
      }
      // Return
      return $array;
    }

    /**
     * Set from fields
     */
    function setFromFields($data) {
      // Loop through fields
      foreach (static::$fields as $field) {
        // If exists
        if (isset($data[$field])) {
          // Set callback
          $callback = 'set' . ucfirst($field);
          // Call
          $this->$callback($data[$field]);
        }
      }
      // Return
      return $this;
    }
  }