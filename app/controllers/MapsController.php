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
      $address = Input::get('address');
      // Locate
      $locate = $address ? Maps::locate($address) : array();
?>
<html>
<head>
<title>Maps</title>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    <?php if ($locate) echo 'map(',json_encode($locate[0]),');'; ?>
  });

  function map(location) {
    // Set map options
    var options = {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      center: new google.maps.LatLng(location.center.lat, location.center.lng),
      zoom: 6
    };
    // Declare gmap
    var map = new google.maps.Map($('#map')[0], options);
    // Mark center
    mark(map, location.center, location.address);
    circle(map, location.center, location.radius);

    var bounds = new google.maps.LatLngBounds(
      new google.maps.LatLng(location.bounds.south,location.bounds.west),
      new google.maps.LatLng(location.bounds.north,location.bounds.east)
    );
    // Find
    map.fitBounds(bounds);
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
      'radius': radius,
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
    height: 800px;
  }
</style>

</head>
<body>
  <?php
  echo Form::open(array('action'=> 'MapsController@home', 'method'=> 'get'));

  echo Form::text('address', $address);
  echo Form::button('Locate', array('type'=> 'submit'));

  echo Form::close();
  ?>
  <div id="map"></div>
</body>
</html>
<?php
    }
  }