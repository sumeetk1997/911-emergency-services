<?php
  include 'config.php';
  include 'header.html';
  session_start();
  if(isset($_POST['login']))
  {
    $username = $_POST['username'];
    $password = $_POST['password'];
	  if($username=='admin'&& $password=='admin')
	  {
      $_SESSION['user'] = 'admin';
		  ?>
      <script src="js/sweetalert.min.js"></script>
			<link rel="stylesheet" type="text/css" href="css/sweetalert.css">
			<script>
				swal({title:"Welcome !",text:"Welcome <?php echo $username; ?>",type:"success"},function(){window.location.href = 'form.php';});
			</script>
      <?php
    }
    else
    {
      $collection = $db -> service;
      $service_db = $collection->findOne(array("vehicles.username"=>$username,"vehicles.password"=>$password));
      if($service_db!=NULL)
      {
        $_SESSION['servicetype'] = $service_db['description'];
        foreach ($service_db['vehicles'] as $vehicle)
        {
          if($vehicle['verified']==TRUE && $vehicle['username']==$username)
          {
            $_SESSION['user'] = 'vehicle_op';
            $_SESSION['username'] = $vehicle['username'];
		        $_SESSION['vehiclenumber'] = $vehicle['vehiclenumber'];
		        $_SESSION['available'] = $vehicle['available'];
		        $_SESSION['longitude'] = $vehicle['coordinates']['longitude'];
		        $_SESSION['latitude'] = $vehicle['coordinates']['latitude'];
            $_SESSION['password'] = $vehicle['password'];
            ?>
            <script src="js/sweetalert.min.js"></script>
            <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
			      <script>
            swal({title:"Welcome !",text:"Welcome <?php echo $username ?>",type:"success"},function(){window.location.href = 'details.php';});
			      </script>
        	  <?php
          }
          else if($vehicle['verified']==FALSE && $vehicle['username']==$username)
	        {
            $_SESSION['user'] = 'vehicle_op';
            $_SESSION['username'] = $vehicle['username'];
            $_SESSION['vehiclenumber'] = $vehicle['vehiclenumber'];
            $_SESSION['available'] = TRUE;
            $_SESSION['longitude'] = $vehicle['coordinates']['longitude'];
            $_SESSION['latitude'] = $vehicle['coordinates']['latitude'];
            $_SESSION['password'] = $vehicle['password'];
            $_SESSION['verify']=$vehicle['telephone'];
            ?>
            <script src="js/sweetalert.min.js"></script>
			      <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
			      <script>
            swal({title:"Welcome !",text:"Welcome <?php echo $username ?>",type:"success"},function(){window.location.href = 'verify.php';});
            </script>
            <?php
          }
        }
      }
      else
      {
        ?>
        <script src="js/sweetalert.min.js"></script>
        <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
        <script>
        swal({title:"Oops...",text:"Invalid Username or Password !",type:"error"},function(){window.location.href = 'login.php';});
        </script>
        <?php
      }
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
					<div class="row">
						<span class="card-title black-text">Login</span>
					</div>
          <div class="row">
            <form class="col s12" method="POST">

              <div class="row">
                <div class="input-field col s6">
                  <i class="material-icons prefix">account_circle</i>
						      <input name="username" id="username" type="text" class="validate" length="10" required>
						      <label for="username">User-Name</label>
						    </div>
					    </div>

              <div class="row">
                <div class="input-field col s7">
                  <i class="material-icons prefix">vpn_key</i>
						      <input name="password" id="password" type="password" class="validate" autocomplete="off" required>
						      <label for="password">Password</label>
						    </div>
					    </div>

              <div class="row" style="margin-left : 15px">
                <input type="checkbox" id="test5" onchange="document.getElementById('password').type = this.checked ? 'text' : 'password'"/>
                <label for="test5">Show Password</label>
              </div>

              <div class="card-action">
                <center>
                  <button class="btn waves-effect waves-light" type="submit" name="login">Login
                    <i class="material-icons right">send</i>
							    </button>
						    </center>
              </div>

              <a href="forgot.php" style ="float : right">forgot password ?</a>

            </form>
          </div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
