<!--DONE-->

<?php
  include 'header.html';
  include 'config.php';
  session_start();
  if(isset($_POST['submit']))
  {
    $email=$_POST['email'];
    $username=$_POST['username'];
    $collection=$db -> service;
    $service_db = $collection->findOne(array("vehicles.username"=>$username,"vehicles.email"=>$email));
    if($service_db !=NULL)
    {
	    foreach($service_db['vehicles'] as $document)
      {
        if($document['email']==$email && $document['username'] == $username)
        {
          $password=$document["password"];
			    require'PHPMailer/PHPMailerAutoload.php';
			    $mail = new PHPMailer;
			    $mail->isSMTP();
			    $mail->SMTPDebug = 1;                                  // Set mailer to use SMTP
			    $mail->Host = 'smtp.gmail.com';  		       // Specify main and backup SMTP servers
			    $mail->SMTPAuth = true;                                // Enable SMTP authentication
			    $mail->Username = 'testprojectit911@gmail.com';        // SMTP username
			    $mail->Password = 'testprojectit';                     // SMTP password
			    $mail->SMTPSecure = 'TLS';                             // Enable TLS encryption, `ssl` also accepted
			    $mail->Port = 587;                                     // TCP port to connect to
			    $mail->setFrom('testprojectit911@gmail.com', '911-no-reply-Verification-code');
			    $mail->addAddress($email, $username);     			// Add a recipient
			    $mail->addAddress($email);             			// Name is optional
			    $mail->addReplyTo('testprojectit911@gmail.com', 'NULL');
			    $mail->isHTML(true);                                    // Set email format to HTML
			    $mail->Subject = 'forgot password';
			    $mail->Body    = 'Your password is:'.$password;
			    $mail->AltBody = 'Your password is:'.$password;
          if(!$mail->send())
          {
            ?>
				    <script src="js/sweetalert.min.js"></script>
				    <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
				    <script>
				    swal("Opps...","Please check your internet connection else try again later.","error");
				    </script>
			     <?php
         }
         else
         {
           ?>
				   <script src="js/sweetalert.min.js"></script>
				   <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
				   <script>
				   swal({title:"Good job!",text:"Password Has Been Send On your Email !",type:"success"},function(){window.location.href = 'login.php';});
				   </script>
				   <?php
         }
       }
     }
   }
	else
  {
		?>
		<script src="js/sweetalert.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/sweetalert.css">
		<script>
		swal("Opps...","Please check your Email-ID and try again later.","error");
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
            <form class="col s12" action="forgot.php" method="POST">
              <div class="row">
                <div class="input-field col s6">
                  <i class="material-icons prefix">account_circle</i>
                  <input name="username" id="username" type="text"  length="10" required class="validate">
                  <label for="username">User-Name</label>
                </div>
              </div>
              <div class="row">
                <div class="input-field col s10">
                  <i class="material-icons prefix">email</i>
                  <input name="email" id="email" type="email" class="validate" required>
                  <label for="email">Enter Email for verification</label>
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
</div>
</body>
<script>
$(document).ready(function()
{
  $('select').material_select();
});
</script>
</html>
