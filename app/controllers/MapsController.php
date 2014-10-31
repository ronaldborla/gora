<?php

  /**
   * Maps controller
   */

  class MapsController extends BaseController {

    /**
     * Maps home
     */
    function home() {

      // If there's address
      $q = Input::get('q');
      // Locate
      $locate = array();

      // Set establishments
      $establishments = array();

      // If there's q
      if ($q) {
        // We're doing a query        

        // Filter q
        $filters = SmsApi::filters($q, array('near', 'within'));

        // Use near
        $useNear = false;

        // Get location
        $location = isset($filters['filters']['within']) ? $filters['filters']['within'] : '';
        // Buf if there's near, then overwrite location
        if (isset($filters['filters']['near']) && $filters['filters']['near']) {
          // Set by near
          $location = $filters['filters']['near'];
          // Use near
          $useNear = true;
        }

        // If there's location, locate the fucker
        $locate = Maps::locate($location);

        // Do query first
        $query = Establishment::fillColumns();
        // If there's query
        if ($filters['query']) {
          // We do a query
          $query = Establishment::search($filters['query'], $query);
        }
        // If there's location
        if ($locate) {
          // Set first 
          $locate = $locate[0];
          // Let's get those within
          if ($useNear) {
            // Use near
            $query = Establishment::near($locate['center']['lat'], $locate['center']['lng'], $locate['radius'], $query);
          } else {
            // Use within
            $query = Establishment::within($locate['center']['lat'], $locate['center']['lng'], $locate['radius'], $query);
          }
        }
        
        $getEstablishments = $query->get();
        // If there are any
        if ($getEstablishments) {
          // Loop
          foreach ($getEstablishments as $establishment) {
            // Add
            $establishments[] = $establishment->toArray();
          }
        }
      }
?>
<html>
<head>
<title>Maps</title>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    <?php 
    if ($locate) {
      echo 'var map = createMap(',json_encode($locate),');',PHP_EOL;
      // If there's any establishments
      if ($establishments) echo 'markAll(map, ',json_encode($establishments),');',PHP_EOL;
    } 
    ?>
  });

  function createMap(location) {
    // Set map options
    var options = {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      center: new google.maps.LatLng(location.center.lat, location.center.lng),
      zoom: 6
    };
    // Declare gmap
    var map = new google.maps.Map($('#map')[0], options);
    // Mark center
    // mark(map, location.center, location.address);
    circle(map, location.center, location.radius);

    var bounds = new google.maps.LatLngBounds(
      new google.maps.LatLng(location.bounds.south,location.bounds.west),
      new google.maps.LatLng(location.bounds.north,location.bounds.east)
    );
    // Find
    map.fitBounds(bounds);
    // Return map
    return map;
  }

  function mark(map, center, title) {
    // Create mark
    return new google.maps.Marker({ 
      'position': new google.maps.LatLng(center.lat, center.lng),
      'title': title,
      'map': map
    });
  }
  function circle(map, center, radius) {
    // Create circle
    return new google.maps.Circle({
      'map': map,
      'center': new google.maps.LatLng(center.lat, center.lng),
      'radius': radius
    });
  }
  function markAll(map, establishments) {
    // Loop
    for (var i in establishments) {
      // Mark
      mark(map, establishments[i], establishments[i].name);
    }
  }
</script>
<style type="text/css">
  body {
    padding: 0px;
    margin: 0px;
  }
  #map {
    width: 100%;
    height: 800px;
  }
</style>

</head>
<body>
  <?php
  echo Form::open(array('action'=> 'MapsController@home', 'method'=> 'get'));

  echo Form::text('q', $q);
  echo Form::button('Locate', array('type'=> 'submit'));

  echo Form::close();
  ?>
  <div id="map"></div>
</body>
</html>
<?php
    }

    /** 
     * Set coords
     */
    function coords() {

      if (Request::isMethod('post')) {

        // Save
        $establishment = Establishment::where('id', '=', intval(Input::get('id')))->first();

        // If there's any
        if ($establishment && $establishment->id) {
          $establishment->lat = Input::get('lat');
          $establishment->lng = Input::get('lng');
          $establishment->address = Input::get('address');
          // Save
          $establishment->save();
        }
      }

      $establishment = Establishment::where('lat', '=', 0)
                                    ->where('lng', '=', 0)
                                    ->orderBy('id', 'asc')
                                    ->first();
      if ($establishment && $establishment->id){
        // Locate
        $locate = Maps::locate($establishment->address);

        // If there's locate
        if ($locate) {
          $locate = $locate[0];
        } else {
          $bounds = Maps::philippines();
          $locate = new Location(array(
            'center'=> Maps::boundsCenter($bounds),
            'radius'=> Maps::boundsRadius($bounds),
            'bounds'=> $bounds,
            'address'=> $establishment->address
          ));
        }
?>
<html>
<head>
<title>Coords</title>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    <?php 
    echo 'var map = createMap(',json_encode($locate->toArray()),');',PHP_EOL;
    ?>
  });

  function createMap(location) {

    // Set map options
    var options = {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      center: new google.maps.LatLng(location.center.lat, location.center.lng),
      zoom: 15
    };
    // Declare gmap
    var map = new google.maps.Map($('#map')[0], options);
    // Mark center
    var m = mark(map, location.center, location.address);

    google.maps.event.addListener(map, 'bounds_changed', function() {
      // Get center
      center = map.getCenter();
      // Set marker
      m.setPosition(center);

      $('form input[name="lat"]').val(center.lat());
      $('form input[name="lng"]').val(center.lng());
    });

    // Return map
    return map;
  }

  function mark(map, center, title) {
    // Create mark
    return new google.maps.Marker({ 
      'position': new google.maps.LatLng(center.lat, center.lng),
      'title': title,
      'map': map
    });
  }
</script>
<style type="text/css">
  body {
    padding: 0px;
    margin: 0px;
  }
  #map {
    width: 100%;
    height: 600px;
  }
</style>

</head>
<body>
  <?php
  echo Form::open(array('action'=> 'MapsController@coords', 'method'=> 'post'));

  echo Form::hidden('id', $establishment->id);
  echo Form::hidden('lat', $establishment->lat);
  echo Form::hidden('lng', $establishment->lng);

  echo '<h3>',e($establishment->name),'</h3>';
  echo Form::text('address', $establishment->address, array('style'=> 'width: 600px;'));
  echo Form::submit('Save');

  echo Form::close();

  ?>
  <div id="map"></div>
</body>
</html>
<?php
      } else {

        echo 'No more establishments left';

      }

    }
  }