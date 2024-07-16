<?php 

require_once("forumdb_conn.php");
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;


///////////GET DOMAIN OR HOMEPAGE///////////////////////
$getdomain = getDomain();
$domain_name = getDomainName();

$user_nf="";$uincorrect="";$pincorrect="";$nullfields="";$temppassword="";$dbemail="";$dbuname="";
	
$page_self = getReferringPage("qstr url");
//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////

if(isset($_COOKIE["form_gate_response"])){
	
	$temppassword = $_COOKIE["form_gate_response"];
	
		
/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

		setcookie("form_gate_response", "", (time() -  1800));

}
									


if(isset($_POST['submit'])){

	$user_details = protect($_POST['email/username']);

	if($user_details){
		
			///////////PDO QUERY////////////////////////////////////	

			$sql = "SELECT * FROM members WHERE USERNAME=? OR  EMAIL=?";
	
			$stmt1 = $pdo_conn_login->prepare($sql);
			$stmt1->execute(array($user_details, $user_details));

			$nrows = $stmt1->rowCount();

			if($nrows){

				$rows = $stmt1->fetch(PDO::FETCH_ASSOC);

				$dbuname = $rows['USERNAME'];
				$dbemail = $rows['EMAIL'];
				$randompwd = generateConfirmationCode();

				$randompwd_unenc = $randompwd;
				
				//////////////ENCRYPT ////////////////
				$randompwd=sha1($randompwd);


				///////////////////////////////////////////////
					
				///////////PDO QUERY////////////////////////////////////	
					
								$sql =  "UPDATE members SET PASSWORD=? WHERE USERNAME=? OR EMAIL = ?";
						
								$stmt2 = $pdo_conn_login->prepare($sql);
								$stmt2->execute(array($randompwd, $dbuname, $dbemail ));



				$temppassword= "<p class='black'><span class='blue'>".$dbuname."</span> your password has been successfully
				 reset.<br/>An email containing your temporary password has been dispatched to your email address: <span class='blue'><a class='links' href='mailto:".$dbemail."'>$dbemail</a></span>,
				It will arrive shortly<br/> We advise you change your password as soon as you 
				<a class='links' href='login'> login</a> <br/>and please do keep your details safe
				<br/> Thank you</p>";

				 
				///EMAIL USER

				$to=$dbemail;
						 $subject = $getdomain."[FORGOT PASSWORD]";
						 $message="Hello ".$dbuname."\n Owing to the request for your login password and having completed the forgot password form at ".$getdomain.", a temporary new password has been generated for you.\n please click <a class='links' href='".$getdomain."/changepassword?email=".$dbemail."&cpi=".$randompwd."'>Here</a> to login with your temporary password:  \n\n NOTE: This new temporary password generated for you is only valid for one login session, hence we advise you change your password as soon as you login.\n\nThank you\n\n\n\n";
									
						 $footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ."  All Rights Reserved.
										 NOTE: This email was sent to you because you completed a forgot password form at ".$getdomain." . if you
										 did not make such a request, please kindly ignore this message.\n\n\n please do not reply to this email.";
						 $headers="from: noreply@".$domain_name."\r\n";
						 sendHTMLMail($to,$subject,$message,$footer,$headers);
						 
								 
				////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
				echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($temppassword)."')</script>";
					

					 
				
			}
			else{				
				
				$get_details = explode ("@",$user_details) ;
				
				if (count($get_details) == 2)
				 $user_nf= "<p class='red'>sorry no such user with the email: <span class='blue'>".$get_details[0]."@".$get_details[1]."</span> was found<br>please try again</p>";

				else
					 $user_nf= "<p class='red'>sorry no such user with the username:   <span class='blue'>".$get_details[0]
				 ."</span> was found<br>please try again</p>";

			 
			}

	}
	else{		
		
		$nullfields='<span class="red">please fill out the EMAIL OR USERNAME field </span>';	
		
	}
	
}




?>



<!DOCTYPE HTML>
<html>
<head>
<title>Forgot Password</title>
<?php require_once("include-html-headers.php")   ?>
<script type="text/javascript">


</script>
<style>
</style>
</head>
<body">
<div class="wrapper">
	<?php  require_once("euromenunav.php"); ?>

	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	echo "<a href='forgotpassword' title=>Password Reset</a> "  ;
			
	?>
	</header>

	<div class="view_user_wrapper" id="hide_vuwbb">
		<h1 class="h_bkg">PASSWORD RESET</h1>
		<div id="loginformcontainer">
			<ul>
				<form method="post" action="forgotpassword">
					<?php  if($temppassword == "") echo "<h3 class='red'>Please enter your username or the email address you used in registering your account with us.</h3>";  ?>
					<?php  if(isset($user_nf)) echo $user_nf ; ?>
					<?php  if(isset($nullfields)) echo $nullfields ;  if($pincorrect != "") echo "<span class='red'>Please enter your email</span>";

					if($uincorrect != "") echo "<span class=red>Please enter your username</span>";
					 ?>
					<?php  if(isset($temppassword)) echo $temppassword ; ?>


					<label>EMAIL OR USERNAME:</label>
					<li><input class="only_form_textarea_inputs"  value="<?php if(isset($pvalue))echo $pvalue; ?>" type='text'  name='email/username'><span class="red"><?php if(isset($pincorrect))echo $pincorrect; ?></span></li>
					<button name="submit" class="formButtons">submit</button>
				</form>
			</ul>
		</div>
	</div>

	<?php require_once('eurofooter.php') ?>
</div>
</body>
</html>