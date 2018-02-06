<!--DONE-->

<?php
  include 'config.php';
  include 'header_op.html';
  session_start();

  if ($_SESSION['user']!='vehicle_op')
  {
  header('Location: index.php');
  }


  if(isset($_POST['submit']))
  {
    $verification_code = $_POST['verification_code'];
    $collection = $db -> service;
    if($verification_code==$_SESSION['verify'])
    {
      $collection->update(array('vehicles.username'=>$_SESSION['username']),array('$set'=>array('vehicles.$.verified' => TRUE)));
      ?>
      <script src="js/sweetalert.min.js"></script>
      <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
      <script>
      swal({title:"Verification Successful !",text:"Welcome <?php echo $_SESSION['username'] ?>",type:"success"},function(){window.location.href = 'details.php';});
      </script>
      <?php
    }
    else
    {
      ?><script src="js/sweetalert.min.js"></script>
      <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
      <script>
      swal({title:"Oops...",text:"Invalid Verification Code !",type:"error"},function(){window.location.href = 'verify.php';});
      </script>
      <?php
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
              <div class="input-field col s7">
                <i class="material-icons prefix">vpn_key</i>
						    <input name="verification_code" id="verification_code" type="text" class="validate" autocomplete="off" required>
						    <label for="vpass">Verification Code ( Registered Telephone Number )</label>
              </div>
            </div>
            <div class="card-action">
              <center>
                <button class="btn waves-effect waves-light" type="submit" name="submit">Submit
                  <i class="material-icons right">send</i>
                </button>
              </center>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
