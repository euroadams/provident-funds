<?php  
 
session_start();
require_once("phpfunctions.php");

 
//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();
	
 //////////CALL FUNTION TO DO FIRST MATCHING IF PERMISSION GRANTED/////////////
//if(isset($_POST["match"])){
	
////////////CHECK FOR FIRST MATCH PERMISSION////////////////////////////////////
///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT ID FROM matching_permit WHERE MATCH_TYPE = 'FIRST_MATCHING' AND MATCH_PERMISSION = 'GRANTED' LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);

	$stmt->execute();
	
	if($stmt->rowCount()){
			
		///////RESTRICT CAPITAL RETURN MATCHING TO WEEKDAYS BTW 6AM AND 3PM ONLY///////////////////////////////////////////////////
		$hour_now = date('h:iA', time());
		$xhour_now = date('h', time());
		$am_pm_now = strtolower(date('a', time()));
		$wkday_now = strtolower(date('l', time()));
		$chk_24hrtime_arr = array("12","11","10","09","08","07","06","05","04");
		
		
		if($wkday_now != "sunday" && $wkday_now != "saturday" ){
											
			if($am_pm_now == "pm"){
				
				/////////IF IS PM ENSURE CAPITAL RETURN MATCHING DOES'NT OCCUR AFTER 3PM//////////////////////////////////////
				if($xhour_now <= 3 || ($xhour_now <= 15 && !in_array($xhour_now, $chk_24hrtime_arr)) ){
					
					doFirstMatching();
					
				}					
				
			}
			elseif($am_pm_now == "am"){
				
				/////////IF IS AM ENSURE CAPITAL RETURN MATCHING DOES'NT OCCUR BEFORE 6AM//////////////////////////////////////				
				if($xhour_now >= 6 ){
					
					doFirstMatching();
					
				}
				
			}

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
						$subject = 'CAPITAL RETURN MATCH NOTIFICATION - '.$domain_name;
						$message = '<b style="color:#ff0000;">ATTENTION ALL ADMINISTRATORS</b><br/> A CAPITAL RETURN MATCHING HAS JUST OCCURED<h2 style="color:#ff0000">TIME OF MATCHING WAS: '.dateFormatStyle(time()).' </h2> ';
						 $to = $receiver_email;									 									 
									
									$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
												NOTE: This email was sent to you because you are part of the administrators team at ".$getdomain.". 
										  please kindly ignore this message if otherwise.\n\n\n please do not reply to this email.\n\n\nThank you";
										  
						 $headers="from: DoNotReply@".$domain_name."\r\n";
						 sendHTMLMail($to,$subject,$message,$footer,$headers);
						 

					
					
			}
			
		}		
		
	}	

/*}
else{
 
	header("location:page-error") ;
 
}*/
 
 ?>