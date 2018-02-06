<?php
  include 'header_op.html';
  include 'config.php';
  session_start();

  if ($_SESSION['user']!='vehicle_op')
  {
  header('Location: index.php');
  }

  if(isset($_POST['reached']))
  {
    $collection = $db -> service;
    $reached_timestamp = new DateTime();
    $collection->update(array("description"=>"log_details",
                              'servicelog._id'=>$_SESSION['p_id']),
                              array('$set'=>array('servicelog.$.Reached_Timestamp' => $reached_timestamp)));
  }


  if(isset($_POST['release']))
  {
    $collection = $db -> service;
    $collection->update(array('vehicles.username'=>$_SESSION['username']),array('$set'=>array('vehicles.$.available' => TRUE)));
	  $R_timestamp = new DateTime();
    $collection->update(array("description"=>"log_details",
                              'servicelog._id'=>$_SESSION['p_id']),
                              array('$set'=>array('servicelog.$.Release_Timestamp' => $R_timestamp)));
    $_SESSION['available'] = TRUE;
  }

if ($_SESSION['available'] == FALSE)
{
	$collection = $db -> service;
	$qry = array("description"=>"log_details","servicelog.assigned_vehiclenumber"=>$_SESSION['vehiclenumber'],"servicelog.Release_Timestamp"=>NULL);
	$logentry = $collection -> findOne($qry);
  if($logentry!=NULL)
  {
    foreach ($logentry['servicelog'] as $person)
    {
      if($person['assigned_vehiclenumber']==$_SESSION['vehiclenumber'] && $person['Release_Timestamp']==NULL)
      {
	$_SESSION['p_id'] = $person['_id'];
?>

	<div class="row"></div>
	<div class="col l12">
		<div class="row"></div>
		<div class="row">
			<div class="col l6 offset-l3">
				<div class="card" >
					<div class="card-content black-text">
						<form method="POST" action="details.php"  onsubmit="return confirm('Do you really want to submit the form?');">
							<div class="row">
								<div class="input-field col s10" >
									<label1 style="color: #40c4ff" for="name">User-Name: </label1><?php echo $_SESSION['username'] ; ?>
								</div>
							</div>
              <div class="row">
								<div class="input-field col s10" >
									<label1 style="color: #40c4ff" for="name">Vehicle Type: </label1><?php echo $_SESSION['servicetype'] ; ?>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s10" >
									<label1 style="color: #40c4ff" for="name">Name: </label1><?php echo $person['name'] ; ?>
								</div>
							</div>
							<div class="row">
            							<div class="input-field col s10">
              								<label1 style="color: #40c4ff" for="gender">Gender: </label1><?php echo $person['gender']; ?>
              							</div>
            						</div>
							<div class="row">
              							<div class="input-field col s10">
                							<label1 style="color: #40c4ff" for="telephone">Contact Number: </label1><?php echo $person['telephone']; ?>
              							</div>
           						 </div>
							<div class="row">
              							<div class="input-field col s10">
                							<label1 style="color: #40c4ff" for="location">Location : </label1>
              							</div>
           						</div>


					<script src="./js/jquery.min.js"></script>
					<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCDREgelhcPpigBLYWGChYeSQ9RU22F2zc&sensor=false&libraries=places"></script>


					<div id="dvDistance"></div>

					<div id="display_map" class="web" style="width:550px;height:350px; margin:auto;"></div>


						<div id="dvPanel" style="width: 500px; height: auto px; margin:auto;"></div>

<?php
  if($person['Reached_Timestamp']==NULL)
  {
 ?>
						<div class="card-action">
            <center>

                    	        					<button class="btn waves-effect waves-light" id="reached" type="submit" name="reached">Reached !
                        	        				<i class="material-icons right">send</i>
									                      </button>
                                        
                  </center>
					      		</div>
                    <?php }
                    else {

                    ?>
                    <div class="card-action">
                    <center>


                                                <button class="btn waves-effect waves-light" type="submit" name="release">JOB COMPLETED
                                                  <i class="material-icons right">send</i>
                                                </button>
                          </center>
                            </div>
                            <?php }?>
						</form>

<?php

}
}
}
}
else
{
  $collection = $db->service;
  $service_db = $collection->findOne(array("vehicles.username"=>$_SESSION['username']));
  if($service_db!=NULL)
  {
    foreach ($service_db['vehicles'] as $vehicle)
    {
      if($vehicle['username']==$_SESSION['username'])
      	$_SESSION['available'] = $vehicle['available'];
    }
  }
?>
	<div class="row"></div>
	<div class="col l12">
		<div class="row"></div>
		<div class="row">
			<div class="col l6 offset-l3">
				<div class="card" >
					<div class="card-content black-text">
						<form method="POST" action="subop.php"  onsubmit="return confirm('Do you really want to submit the form?');">

							<div class="row">
								<div class="input-field col s10" >
									<label1 style="color: #40c4ff" for="name">User-Name: </label1><?php echo $_SESSION['username'] ; ?>
								</div>
							</div>
              <div class="row">
								<div class="input-field col s10" >
									<label1 style="color: #40c4ff" for="name">Vehicle Type: </label1><?php echo $_SESSION['servicetype'] ; ?>
								</div>
							</div>
							<div class="row">
								<?php echo "You currently have NO JOB assigned !";?>


<?php
	header("refresh: 3;");
}
?>


<script>
function mapLocation() {
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;

    function initialize() {
        directionsDisplay = new google.maps.DirectionsRenderer();
        var pune = new google.maps.LatLng(18.5204303, 73.85674369999992);
        var mapOptions = {
            zoom: 13,
            center: pune
        };
        map = new google.maps.Map(document.getElementById('display_map'), mapOptions);
        directionsDisplay.setMap(map);
	directionsDisplay.setPanel(document.getElementById('dvPanel'));

	var latitude2 = parseFloat("<?php echo $person['coordinates']['dest_latitude']; ?>"); // Latitude get from above variable
	var longitude2 = parseFloat("<?php echo $person['coordinates']['dest_longitude']; ?>"); // Longitude from same
	var latitude1 = parseFloat("<?php echo $_SESSION['latitude']; ?>"); // Latitude get from above variable
	var longitude1 = parseFloat("<?php echo $_SESSION['longitude']; ?>"); // Longitude from same
        var start = new google.maps.LatLng(latitude1, longitude1);
        var end = new google.maps.LatLng(latitude2, longitude2);

        var bounds = new google.maps.LatLngBounds();
        bounds.extend(start);
        bounds.extend(end);
        map.fitBounds(bounds);
        var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
                directionsDisplay.setMap(map);
            } else {
                alert("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
            }
        });
       // FOR DURATION
	var service = new google.maps.DistanceMatrixService();
    service.getDistanceMatrix({
        origins: [start],
        destinations: [end],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC,
        avoidHighways: false,
        avoidTolls: false
    }, function (response, status) {
        if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
            var distance = response.rows[0].elements[0].distance.text;
            var duration = response.rows[0].elements[0].duration.text;
            var dvDistance = document.getElementById("dvDistance");
        } else {
            alert("Unable to find the distance via road.");
        }
    });

    }

    google.maps.event.addDomListener(window, 'load', initialize);
}
mapLocation();
</script>

</body>
</html>
