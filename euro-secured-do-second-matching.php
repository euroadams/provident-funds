<?php  
 
session_start();
require_once("forumdb_conn.php");
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();


$username = $_SESSION["username"];		

$page_self = 'investment-return-match';
 
if(getUserPrivilege($username) == "ADMIN"){
		
	//////////CALL FUNTION TO DO SECOND MATCHING IF PERMISSION GRANTED/////////////
	if(isset($_POST["match"])){
		
	////////////CHECK FOR FIRST MATCH PERMISSION////////////////////////////////////
	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT ID FROM matching_permit WHERE MATCH_TYPE = 'SECOND_MATCHING' AND MATCH_PERMISSION = 'GRANTED' LIMIT 1";

		$stmt = $pdo_conn_login->prepare($sql);

		$stmt->execute();
		
		if($stmt->rowCount()){
			
			//////////////doSecondMatching();///////ADMINS SHOULD DO A MANUAL INVESTMENT RETURN MATCHING/////////////////////////////
			
			///////////////SEND CAPITAL RETURN MATCHING NOTIFICATION TO ALL ADMINS/////////////////////////////////////////////////////////////
			//////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT EMAIL, USERNAME FROM members WHERE USER_PRIVILEGE='ADMIN'";

			$stmt1 = $pdo_conn_login->prepare($sql);

			$stmt1->execute(array());
			
			while($admin_rows = $stmt1->fetch(PDO::FETCH_ASSOC)){	

					$receiverusername = $admin_rows["USERNAME"];
					$receiver_email = $admin_rows["EMAIL"];
				/////////////SEND ADMINS PM///////////////////////////////////
				///////////PDO QUERY////////////////////////////////////	
				/*	
					$sql = "INSERT INTO privatemessage (INBOX,TIME,SENDER,USERNAME,COPY_OF_INBOX,DATE,MESSAGE_SUBJECT) VALUES(?,?,?,?,?,?,?)";

					$stmt4 = $pdo_conn_login->prepare($sql);

					$stmt4->execute(array($message,$timesent,$sender,$receiverusername,$message,$datesent,$subject));		
				*/
				////////////SEND ADMINS EMAIL/////////////////////////////////////
						$subject = 'INVESTMENT RETURN MATCH REMINDER - '.$domain_name;
						$message = '<b style="color:#ff0000;">ATTENTION ALL ADMINISTRATORS</b><br/> This is to notify and remind you that you should do an INVESTMENT RETURN MATCHING today<h2 style="color:#ff0000">PLEASE DO THE MATCHING ON WEEKDAYS ONLY
									BETWEEN THE HOURS OF 6AM AND 3PM<br/> IF THIS REMINDER FALLS ON WEEKEND PLEASE DON\'T DO THE MATCHING UNTIL ON MONDAY BY 6AM. </h2> ';
						 $to = $receiver_email;									 									 
									
									$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
												NOTE: This email was sent to you because you are part of the administrators team at ".$getdomain.". 
										  please kindly ignore this message if otherwise.\n\n\n please do not reply to this email.\n\n\nThank you";
										  
						 $headers="from: DoNotReply@".$domain_name."\r\n";
						 sendHTMLMail($to,$subject,$message,$footer,$headers);
						 
						 
										
					
			}		
			
			
		}		

	}
	///////IF ADMINS REQUESTS MANUAL INVESTMENT RETURN MATCHING/////////////////////////////
	elseif(isset($_POST["manual_match"])){
		
		$avn = protect($_POST["avn"]);
		
		if(verifyAVN($username,$avn)){				
			
			doSecondMatching();
			
			
			///////////////SEND CAPITAL RETURN MATCHING NOTIFICATION TO ALL ADMINS/////////////////////////////////////////////////////////////
			//////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT EMAIL, USERNAME FROM members WHERE USER_PRIVILEGE='ADMIN'";

			$stmt1 = $pdo_conn_login->prepare($sql);

			$stmt1->execute(array());
			
			while($admin_rows = $stmt1->fetch(PDO::FETCH_ASSOC)){	

					$receiverusername = $admin_rows["USERNAME"];
					$receiver_email = $admin_rows["EMAIL"];
				/////////////SEND ADMINS PM///////////////////////////////////
				///////////PDO QUERY////////////////////////////////////	
				/*	
					$sql = "INSERT INTO privatemessage (INBOX,TIME,SENDER,USERNAME,COPY_OF_INBOX,DATE,MESSAGE_SUBJECT) VALUES(?,?,?,?,?,?,?)";

					$stmt4 = $pdo_conn_login->prepare($sql);

					$stmt4->execute(array($message,$timesent,$sender,$receiverusername,$message,$datesent,$subject));		
				*/
				////////////SEND ADMINS EMAIL/////////////////////////////////////
						$subject = 'INVESTMENT RETURN MATCH NOTIFICATION - '.$domain_name;
						$message = '<b style="color:#ff0000;">ATTENTION ALL ADMINISTRATORS</b><br/><br/> AN INVESTMENT RETURN MATCHING HAS JUST OCCURED<h2 style="color:#ff0000">TIME OF MATCHING WAS: '.dateFormatStyle(time()).' </h2> ';
						 $to = $receiver_email;									 									 
									
									$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
												NOTE: This email was sent to you because you are part of the administrators team at ".$getdomain.". 
										  please kindly ignore this message if otherwise.\n\n\n please do not reply to this email.\n\n\nThank you";
										  
						 $headers="from: DoNotReply@".$domain_name."\r\n";
						 sendHTMLMail($to,$subject,$message,$footer,$headers);
						 
						 $alert_user = '<span class="green">YOU HAVE SUCCESSFULLY EXECUTED THE INVESTMENT RETURN MATCHING</span>';						 
						 
						 
										
					
			}
				
		}	
		else
			$alert_user = '<span class=" errors blink">SORRY AVN VERIFICATION FAILED</span>';
		

		////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
			echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert_user)."')</script>";
				
	}

}
else{
	
	header("location:page-error") ;
 
}

 
 ?>