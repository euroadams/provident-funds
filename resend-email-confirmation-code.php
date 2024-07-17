
<?php 

require_once('phpfunctions.php');

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();

	$referral_email_link="";

if(isset($_POST["email"]) || isset($_GET["email"])){


	if(isset($_POST["email"]))
		$email = protect($_POST['email']);

	if(isset($_GET["email"]))
		$email = protect($_GET['email']);
	
	if(isset($_POST["rise"]))
		$rise = protect($_POST['rise']);

	if(isset($_GET["rise"]))
		$rise = protect($_GET['rise']);

	if(isset($_GET["rdr"]))
		$rdr = "location:".protect($_GET["rdr"])."?code-resend";
	
	if(isset($rise) && $rise)
		$referral_email_link =  'rise='.$rise.'&';

	///////////PDO QUERY////////////////////////////////////	
		
					$sql = "SELECT EMAIL_CONFIRMATION_CODE  FROM members WHERE EMAIL = ?";

					$stmt1 = $pdo_conn_login->prepare($sql);
					$stmt1->execute(array($email));
					
	$row = $stmt1->fetch(PDO::FETCH_ASSOC);
	
	$confirmcode = $row['EMAIL_CONFIRMATION_CODE'];
	
	///SEND CONFIRMATION EMAIL TO USER/////

	$to=$email;
			 $subject="Confirm your Email for registration at ".$getdomain;
			 $message="Hello \n\n Thank you for choosing to register an account with us\nPlease click on the following link to comfirm your email and continue with your registration\n <a class='links' href'".$getdomain."/register?".$referral_email_link."email=".$email."&code=".$confirmcode."'>Confirm Your Email</a> \nThank you\n\n\n\n";
						
						$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
									NOTE: This email was sent to you because you are about to register an account at ".$getdomain." with this email address. If you
							  did not make such registration request, please kindly ignore this message.\n\n\n Please do not reply to this email.\n\n\nThank you";
							  
			 $headers="from: DoNotReply@".$domain_name."\r\n";
			 sendHTMLMail($to,$subject,$message,$footer,$headers);
			 

			 
			 echo "<span class=black> your confirmation link has been resent to your email: <a href='mailto:".$email."' class=cyan>".$email."</a>.<br/>Thank you.</span>";
	/////////////////////////////////////////////////////////////	
		


}


if(isset($_GET["rdr"]))
	header($rdr);

?>

