

<?php

if(isset($_POST['btn-signup']))
{
	$keyword = $_POST['keyword'];
	$Aid = $_POST['Aid'];
        	  $service = $_POST['servicetype'];
	//$upass = $_POST['pass'];

	// connect to mongodb
   $con = new MongoClient();

//==========================
	if($con)
	{

		//connecting to database
		$databse=$con->emergency;
		echo "Database selected";

		//connect to specific collection
		$collection=$databse->service;
		echo "Collection service Selected succsessfully";

		$problem=array('type'=>$keyword,'Aid'=>$Aid);

                echo $symptoms;
		//checking for existing user
		$count=$collection->findOne($symptoms);
 //$collection->update(array("description"=>$service),array('$push'=>array("vehicles" => $vehicle_details)),array('upsert'=>TRUE));

                if(!count($count))
		{
			//Save the New symptom
			$symptoms=array('type'=>$keyword,'Aid'=>$Aid,'service'=>$service);

      $collection->update(array("description"=> $service),array('$push'=>array("problem" => $problem)),array('upsert'=>TRUE));
			//echo "You are successfully registered.";
			?>
        <script>alert('Added successfully ');</script>
        <?php
		}
		else
		{
			echo "Already existed.";
			?>
        <script>alert('error');</script>
        <?php
		}

	}else
	{

		die("Database are not connected");
	}

//========


}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Search System</title>
<link rel="stylesheet" href="style.css" type="text/css" />

</head>
<body>
<center>
<div id="Search-form">
<form method="POST">
<table align="center" width="30%" border="0">
<tr>
<td><input type="text" name="keyword" placeholder="Add here" required /></td>
</tr>
<tr>
<td><input type="text" name="Aid" placeholder="Aid" required /></td>
</tr>


<tr>
<td>
                  <select name="servicetype" id="servicetype" >
                    <option value="1" disabled selected>Choose Your Service Type</option>
                    <option value="ambulance">Ambulance</option>
                    <option value="police">Police</option>
                    <option value="firebrigade">Fire-Brigade</option>
                  </select>
</td>
</tr>

<tr>
<td><button type="submit" name="btn-signup">Add to DB</button></td>
</tr>

<tr>
<td><a href="in.php">Search Here</a></td>
</tr>
</table>
</form>
</div>
</center>
</body>
</html>
