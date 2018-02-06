<!--DONE-->

<?php
  include'header_op.html';
  include 'config.php';
  session_start();

  if ($_SESSION['user']!='vehicle_op')
  {
    header('Location: index.php');
  }

  if(isset($_POST['changepass']))
  {
		$oldpass=$_POST['oldpass'];
    $newpass=$_POST['newpass'];
		$confirm=$_POST['confirm'];
		$collection = $db-> service;
		if($_SESSION['password']==$oldpass)
		{
			if($newpass==$confirm)
			{
				$collection->update(array('vehicles.username'=>$_SESSION['username']),array('$set'=>array('vehicles.$.password' => $newpass)));
				$_SESSION['password']= $newpass;
				?>
				<script src="js/sweetalert.min.js"></script>
				<link rel="stylesheet" type="text/css" href="css/sweetalert.css">
				<script>
				    swal({title:"Good job",text:"password has been changed succesfully",type:"success"},function(){window.location.href = 'subop.php';});
				</script>
			  <?php
			}
			else
			{
				?>
 				<script src="js/sweetalert.min.js"></script>
				<link rel="stylesheet" type="text/css" href="css/sweetalert.css">
				<script>
				swal({title:"Oops...",text:"password doesnt match!  Enter again",type:"error"},function(){window.location.href = 'changepass.php';});
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script src="js/sweetalert.min.js"></script>
			<link rel="stylesheet" type="text/css" href="css/sweetalert.css">
			<script>
			swal({title:"Oops...",text:"wrong password...! Enter again",type:"error"},function(){window.location.href = 'changepass.php';});
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
						<span class="card-title black-text">Change Password</span>
					</div>
				  <div class="row">
						<form class="col s12" method="POST">
							<div class="row">
								<div class="input-field col s6">
									<i class="material-icons prefix">account_circle</i>
									<input name="oldpass" id="oldpass" type="text" class="validate" length="10" required>
									<label for="oldpass">Old-Password</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s6">
									<i class="material-icons prefix">account_circle</i>
									<input name="newpass" id="newpass" type="text" class="validate" length="10" required>
									<label for="newpass">Enter New-Password</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s6">
									<i class="material-icons prefix">account_circle</i>
									<input name="confirm" id="confirm" type="text" class="validate" length="10" required>
									<label for="confirm">confirm New-Password</label>
								</div>
							</div>
							<div class="card-action">
								<center>
									<button class="btn waves-effect waves-light" type="submit" name="changepass">change
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
</div>
</body>
</html>
