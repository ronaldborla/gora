<?php

  /**
   * Maps
   */

  class Maps {

    /**
     * Phils bounds
     */
    static function philippines() {
      // Return coords
      return new Bounds(array(
        'north'=> 19.5740241,
        'east'=> 126.6043837,
        'south'=> 4.5870339,
        'west'=> 116.7029193
      ));
    }

    /**
     * Get center of bounds
     */
    static function boundsCenter(Bounds $bounds) {
      // Return
      return new LatLng(array(
        'lat'=> $bounds->south + (($bounds->north - $bounds->south) / 2),
        'lng'=> $bounds->west + (($bounds->east - $bounds->west) / 2)
      ));
    }

    /**
     * Get radius
     */
    static function boundsRadius(Bounds $bounds) {
      // Get center
      $center = static::boundsCenter($bounds);
      // Return
      return static::getDistanceBetweenPoints($center->lat, $center->lng, $bounds->north, $bounds->east);
    }

    /**
     * Get distance
     */
    static function getDistanceBetweenPoints($y1, $x1, $y2, $x2) {
      // Calculate distance
      $distance = rad2deg(acos(
        (sin(deg2rad($y1)) * sin(deg2rad($y2))) + 
        (cos(deg2rad($y1)) * cos(deg2rad($y2)) * cos(deg2rad($x1 - $x2)))
      )) * 
      // Multiply with seconds
      60 * 1.1515 * 
      // Get meters
      1.609344 * 1000;
      return $distance;
    }

    /**
     * Locate an address
     */
    static function locate($address, Bounds $bounds = null) {
      // If there's no base
      if (!$bounds) {
        // Use philippines
        $bounds = static::philippines();
      }
      // Url
      $url = 'https://maps.googleapis.com/maps/api/geocode/json';
      // Args
      $args = array(
        'address'=> $address,
        'bounds'=> $bounds->north.','.$bounds->east.'|'.$bounds->south.','.$bounds->west
        //'key'=> App::$config['gmap']['key'],
      );
      // Request url
      $request = $url . '?' . http_build_query($args);
      // Get results
      $json = @json_decode(@file_get_contents($request), true);

      // If there are results
      if (isset($json['results']) && $json['results']) {
        // Get distance from center of davao
        $distances = array();
        // Loop through results
        foreach ($json['results'] as $i=> $result) {
          // If location is set
          if (isset($result['geometry']['location'])) {
            // Get location
            $location = new LatLng($result['geometry']['location']);

            // If location is within bounds
            if ($location->lat >= $bounds->south && $location->lat <= $bounds->north &&
                $location->lng >= $bounds->west && $location->lng <= $bounds->east) {
              // Get center
              $center = static::boundsCenter($bounds);
              // Calculate radius
              $radius = static::getDistanceBetweenPoints($center->lat, $center->lng, $location->lat, $location->lng);
              // Set radius
              $distances[$i] = $radius;
            }
          }
        }
        // If there's distances
        if ($distances) {
          // Set locations
          $locations = array();
          // Sort
          asort($distances);
          // Loop through distances
          foreach ($distances as $i=> $distance) {
            // Get geometry
            $geometry = $json['results'][$i]['geometry'];
            // Set center
            $center = new LatLng($geometry['location']);
            // Set default radius
            $radius = 200;
            // Get bounds
            $locationBounds = isset($geometry['bounds']['southwest']) ? $geometry['bounds'] : array();

            // If still not set
            if (!$locationBounds) {
              // Get from viewport
              $locationBounds = isset($geometry['viewport']['southwest']) ? $geometry['viewport'] : array();
            }

            // If there's bounds
            if ($locationBounds) {
              // Overwrite location bounds
              $locationBounds = new Bounds(array(
                'north'=> $locationBounds['northeast']['lat'],
                'east'=> $locationBounds['northeast']['lng'],
                'south'=> $locationBounds['southwest']['lat'],
                'west'=> $locationBounds['southwest']['lng']
              ));
              // Get center
              $center = static::boundsCenter($locationBounds);
              // Calculate radius
              $radius = static::getDistanceBetweenPoints($center->lat, $center->lng, $locationBounds->north, $locationBounds->east);
            }

            // Set location
            $locations[] = new Location(array(
              // Set center
              'center'=> $center,
              // Set radius
              'radius'=> $radius,
              // Set bounds
              'bounds'=> $locationBounds,
              // Set address name
              'address'=> isset($json['results'][$i]['formatted_address']) ? $json['results'][$i]['formatted_address'] : ''
            ));
          }

          // Return locations
          return $locations;
        }
      }
      // Return empty
      return array();
    }

  }