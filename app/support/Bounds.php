<?php

  /**
   * Bounds
   */

  class Bounds extends MapsData {

    // Fields
    protected static $fields = array(
      'north',
      'east',
      'south',
      'west'
    );

    // Get center
    function getCenter() {
      // Return
      return Maps::boundsCenter($this);
    }

    // Get radius
    function getRadius() {
      // Return
      return Maps::boundsRadius($this);
    }
  }