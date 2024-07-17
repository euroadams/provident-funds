
<?php 

require_once('phpfunctions.php');

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();
	
/////////HANDLE FOR RESENDING ACCOUNT ACTIVATION LINK FROM LOGIN PAGE/////////////////////////////////////////////////////////////////////	

if(isset($_POST["username"]) || isset($_GET["user"])){

	if(isset($_POST["username"]))
		$username = $_POST['username'];

	if(isset($_GET["user"]))
		$username = $_GET['user'];

	if(isset($_GET["rdr"]))
		$rdr = "location:".$_GET["rdr"]."?code-resend";

	///////////PDO QUERY////////////////////////////////////	
		
					$sql = "SELECT * FROM members WHERE USERNAME=?";

					$stmt1 = $pdo_conn_login->prepare($sql);
					$stmt1->execute(array($username));

	$row = $stmt1->fetch(PDO::FETCH_ASSOC);

	$email=$row['EMAIL'];
	$confirmcode=$row['ACC_CONFIRMATION_CODE'];
	///SEND ACCOUNT ACTIVATION EMAIL TO USER

	$to=$email;
			 $subject="Activate your account at ".$getdomain;
			 
			 $message="Hello ".$username."\n Thank you for registering an account with us\nplease click on the following link to activate your account\n <a class='links' href='".$getdomain."/activate-account?username=".$username."&code=".$confirmcode."' >Activate Your Account</a> \nThank you\n\n\n\n";
						
			 $footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ."  All Rights Reserved.
						NOTE: this email was sent to you because you registered an account at ".$getdomain." . if you
							  did not make such registration, please kindly ignore this message.\n\n\n please do not reply to this email.\n\n\nThank you";
			 $headers="from: DoNotReply@".$domain_name."\r\n";
			 sendHTMLMail($to,$subject,$message,$footer,$headers);
			 

			 
			 echo "<span class='blue'>".$username."</span><span class='black'> your activation link has been resent.<br/>Thank you.</span>";
	/////////////////////////////////////////////////////////////	
		


	if(isset($_GET["rdr"]))
		header($rdr);

}




//////////////IF ACTIVATION LINK IS CLICKED VIA EMAIL///////////////////////////////////////////////////////////////////////////////////

if(isset($_GET["username"])  && isset($_GET["code"]) ){
	
	$username = $_GET['username'];
	$code = $_GET['code'];


	if($username && $code){

	///////////PDO QUERY////////////////////////////////////	

			$sql = "SELECT * FROM members WHERE USERNAME=?";

			$stmt2 = $pdo_conn_login->prepare($sql);
			$stmt2->execute(array($username));

			$row = $stmt2->fetch(PDO::FETCH_ASSOC);

			$dbcode = $row['ACC_CONFIRMATION_CODE'];


			if($code == $dbcode){
				
				$confirmed = "ACTIVATED";
				
				$confirmcode = $dbcode;


			///////////PDO QUERY////////////////////////////////////	
	
				$sql = "UPDATE members SET ACCOUNT_STATUS = 'ACTIVATED', ACC_CONFIRMATION_CODE = '0' WHERE USERNAME=?";

				$stmt3 = $pdo_conn_login->prepare($sql);
				$stmt3->execute(array($username));
					
				///SEND CONFIRMATION EMAIL USER

				$email=$row['EMAIL'];

				$to=$email;

				//$passwordmailed=$row['PASSWORD'];

				///////FETCH THE USER TMP PWD AND MAIL TO HIM THEN DEL IT//////////////////////////////////////////////////////////////////////////////////////////
				/*
				$path = "LOCAL COOKIES/TMP/".$username."_tmp_pwd.txt";
				$opened = fopen($path, "r");
				$passwordmailed = fread($opened, filesize($path));
				fclose($opened);

				if(realpath($path))
					unlink($path);*/

						 $subject="Your Login Details at ".$getdomain;

						 $message="Hello ".$username."\n Thank you for activating your account.\n your registration is now complete\n please click <a class='links' href='".$getdomain."/login'>here</a> to log into your account with your details. \n USERNAME: ".$username."\nPASSWORD: your password \n\nPlease do keep your details safe.  \n\n\nThank you\n\n\n\n";
									
						 $footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ."  All Rights Reserved.
									NOTE: this email was sent to you because you registered an account at ".$getdomain." . if you
										  did not make such registration, please kindly ignore this message.\n\n\n please do not reply to this email.";
						 $headers="from: DoNotReply@".$domain_name."\r\n";

						 sendHTMLMail($to,$subject,$message,$footer,$headers);
						 

				//////////////SEND AVN AFTER ACTIVATION OF ACCOUNT///////////////////////////////////////////////	
					
					sendAVN($username);
					
					
					$alert_user = "Thank you for activating your account.<br/>your account is now fully active please click <a href='login'> here</a> to login now<br/>
					
					Also an E-mail containing your login details and your ACCOUNT VERIFICATION NUMBER(AVN) has been dispatched to your E-mail address: <a href='mailto:".$email."'>".$email."</a><br/>Please do keep your details safe<br/>Thank You";
					
				
			}

			else{
				
				if($dbcode=='0'){
					
					$alert_user = "your account has already been activated please click <a href='login'> here</a> to login";
					
				}
				else if($code != $dbcode){
					 
					$alert_user = "<span class='red'>It seems your activation link was altered!!!</br>Please kindly go back to your email and click on the link again</br>

					and please do not alter the link to enable you activate your account without any issues</br>Thank you.</span>";

				}

			}
		}
		else
			$alert_user = "<span class='red'>An unexpected error has occurred<br/>We are sorry about this.</span>";
			

}


?>
<!DOCTYPE HTML>
<head>
<title>Account Activation </title>

<?php require_once('include-html-headers.php')   ?>

<style>

</style>

</head>
<body >
<div class="wrapper" id="go_up">

		<?php require_once('euromenunav.php')   ?>

		<header class="mainnav">
			<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>
			<a href='activate-account' title=>Account Activation</a> 
				
		</header>
		<!--<div class="postul">(<a class='links topagedown' href="#go_down">Go Down</a>)</div>-->
		<div class='view_user_wrapper' id="hide_vuwbb">
			<h1 class="h_bkg">ACCOUNT ACTIVATION</h1>
			<div class="type_b">
				
					<span class='cyan'><?php if(isset($alert_user)) echo $alert_user; ?></span>
			</div>
				
		</div>
		<!--<div class="postul">(<a class='links topageup' href="#go_up">Go Up</a>)</div>
		<span id="go_down"></span>-->

		<?php require_once('eurofooter.php')   ?>
</div>
</body>
</html>