<?php

  /**
   * Location object
   */

  class Location extends MapsData {

    // Set valid fields
    protected static $fields = array(
      'center',
      'radius',
      'bounds',
      'address'
    );

    /**
     * To array
     */
    function toArray() {
      // Return
      return array(
        'center'=> $this->center->toArray(),
        'radius'=> $this->radius,
        'bounds'=> $this->bounds->toArray(),
        'address'=> $this->address
      );
    }

    /**
     * Set
     */
    function setCenter($center) {
      // Check if instance of LatLng
      if ($center instanceof LatLng) {
        // Set
        $this->data['center'] = $center;
      } else {
        // Create new LatLng
        $this->data['center'] = new LatLng($center);
      }
      // Return
      return $this;
    }

    /**
     * Set bounds
     */
    function setBounds($bounds) {
      // If instnace of bounds
      if ($bounds instanceof Bounds) {
        // Set
        $this->data['bounds'] = $bounds;
      } else {
        // Create new bounds
        $this->data['bounds'] = new Bounds($bounds);
      }
      // Return
      return $this;
    }

    /**
     * Set address
     */
    function setAddress($address) {
      // Set address
      $this->data['address'] = (string) $address;
      // Return
      return $this;
    }
  }