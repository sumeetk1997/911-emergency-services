<?php
include 'header_deploy.html';
include 'config.php';
session_start();

if ($_SESSION['user']!='admin')
{
header('Location: index.php');
}

$collection = $db -> service;

$reason_aid = $collection -> findOne(array("problem.type"=>$_SESSION['reason']));
foreach ($reason_aid['problem'] as $doc) {
	if($doc['type'] == $_SESSION['reason'])
		$aid = $doc ['Aid'];
}

$qry = array("description"=>"log_details","servicelog.assigned_vehiclenumber"=>$_SESSION['vehiclenumber'],"servicelog.Release_Timestamp"=>NULL);
$logentry = $collection -> findOne($qry);
if($logentry!=NULL)
{
	foreach ($logentry['servicelog'] as $person)
	{
		if($person['assigned_vehiclenumber']==$_SESSION['vehiclenumber'] && $person['Release_Timestamp']==NULL)
		{

?>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<div class="row"></div>
	<div class="col l12">
		<div class="row"></div>
		<div class="row">
			<div class="col l6 offset-l3">
				<div class="card" >
					<div class="card-content black-text">
						<div class="row">
							<div class="input-field col s10" >
								<label1 style="color: #40c4ff" for="name">YOU HAVE APPROXIMATELY UNTIL HELP TO ARRIVE: </label1>
                					</div>
						</div>
						<div class="row">
							<center>
							    <div id="clockdiv">
              							<div class="cl">
    							    					<span id="minutes"></span>
    									    			<div class="smalltext">Minutes</div>
														</div>
														<div class="cl">
    							    					<span id="seconds"></span>
    									    			<div class="smalltext">Seconds</div>
														</div>
							    </div>
								</center>
           		</div>

							<?php
							if(isset($_POST['estimated_duration']))
							  {
								$collection = $db -> service;
								$duration = $_POST['estimated_duration'];
								//$col->insert($qry);
								$collection->update(array("description"=>"log_details","servicelog._id"=>$person['_id']),
																			array('$set'=>array("servicelog.$.estimated_duration"=>$duration)));
								?>
							  <script src="js/sweetalert.min.js"></script>
							  <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
							  <script>
								swal({title:"Good job!",text:"Service Completed",type:"success"},function(){window.location.href = 'form.php';});
							  </script>
					      <?php
							}
							?>

<form method ="POST" name="myform" >
	<table>
		<tr>
	<input type="hidden" id="estimated_duration" name="estimated_duration" class=" input-field col s3" required></td>
</tr>
</table>
<div class="card-action">
                 <center>
                   <button class="btn waves-effect waves-light" type="submit">SERVICE DONE !
                     <i class="material-icons right">send</i>
                   </button>
                 </center>
               </div>

</form>

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
				                 							<label1 style="color: #40c4ff" >REASON TO CALL FOR HELP : </label1><?php echo $person['reason']; ?>
				               							</div>
				            						 </div>


																 <div class="row">
									               							<div class="input-field col s10">
									                 							<label1 style="color: #40c4ff" >AID : </label1><?php echo $aid; ?>
									               							</div>
									            						 </div>

							<div class="row">
              							<div class="input-field col s10">
                							<label1 style="color: #40c4ff" for="location">Location : </label1>
              							</div>
           						</div>

					<script src="./js/jquery.min.js"></script>
					<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCDREgelhcPpigBLYWGChYeSQ9RU22F2zc&sensor=false&libraries=places"></script>
					<div id="display_map" class="web" style="width:550px;height:350px; margin:auto;"></div>
 					<div id="dvDistance"></div>

<p id="demo"></p>
<script type="text/javascript">
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
	var longitude2 = parseFloat("<?php echo $person[coordinates]['dest_longitude']; ?>"); // Longitude from same
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
      	    var seconds = 60;
      	    var minutes = parseInt(duration);
						document.getElementById('estimated_duration').value = minutes;
						document.getElementById('minutes').innerHTML = --minutes;

      		setInterval(function(){
					if (seconds < 1) {
						if (minutes <= 0) {
          document.forms["myform"].submit();
				}
																else {



document.getElementById('minutes').innerHTML = --minutes;
																		seconds = 60;
																		document.getElementById('seconds').innerHTML = --seconds;
							 }
						 }
				        else {
					  document.getElementById('seconds').innerHTML = --seconds;
						 }
					},
					1000
				  );

        } else {
            alert("Unable to find the distance via road.");
        }
    });

    }

    google.maps.event.addDomListener(window, 'load', initialize);
}
mapLocation();

</script>
<?php
}
}
} ?>
</body>
</html>
