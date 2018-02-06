<?php
session_start();
if ($_SESSION['user']=='admin') {
  include 'header_admin.html';
}
else if ($_SESSION['user']=='vehicle_op') {
  include 'header_op.html';
}
else{
  include 'header.html';
}
?>
<style>
body, html {
  height: 100%;
  width: 100%;
}
div#map {
  width: 100%; height: 94%;
}
</style>
<script>
// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
  var map;
  var infoWindow;
  var service;
  function initMap()
  {
    map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 18.5204303, lng: 73.85674369999992},
          zoom: 15});
    infoWindow = new google.maps.InfoWindow();
    service = new google.maps.places.PlacesService(map);
    // The idle event is a debounced event, so we can query & listen without
    // throwing too many requests at the server.
    map.addListener('idle', performSearch);
  }
  function performSearch()
  {
    var request =
    {
      bounds: map.getBounds(),
      keyword: 'police'
    };
    service.radarSearch(request, callback);
  }
  function callback(results, status)
  {
    if (status !== google.maps.places.PlacesServiceStatus.OK)
    {
      console.error(status);
      return;
    }
    for (var i = 0, result; result = results[i]; i++)
    {
      addMarker(result);
    }
  }
  function addMarker(place)
  {
    var marker = new google.maps.Marker({
                 map: map,
                 position: place.geometry.location,
                 icon: 'images/marker.png'
                 });
    google.maps.event.addListener(marker, 'click', function() {
      service.getDetails(place, function(result, status) {
        if (status !== google.maps.places.PlacesServiceStatus.OK)
        {
          console.error(status);
          return;
        }
        infoWindow.setContent(result.name);
        infoWindow.open(map, marker);
      });
    });
  }
</script>
</head>
<body>
  <div id="map"></div>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDREgelhcPpigBLYWGChYeSQ9RU22F2zc&callback=initMap&libraries=places,visualization" async defer></script>
</body>
</html>
