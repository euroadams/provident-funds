<?php


session_start();
require_once("forumdb_conn.php");
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();

setPageTimeZone();

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$message_field_err="";$message="";$asterix_all="";

			
$page_self = getReferringPage("qstr url");
//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////

if(isset($_COOKIE["form_gate_response"])){
	
	$data = $_COOKIE["form_gate_response"];
	
		
/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

		setcookie("form_gate_response", "", (time() -  1800));

}

						

if(isset($_POST["offender"])){

		$offender = $_POST["offender"];
	
	
}


////////////////////////ON SEND/////////////////////////////////////////////////////////////////////

if(isset($_POST['make_report'])){

		if($_SESSION["username"]){
			

		$sender=$_SESSION['username'];
		$message=protect($_POST['composedmessage']);
		$timesent=time();
		

		if($message && $offender){
		
			//////////PREVENT REFRESH PAGE EFFECT////////////////////////////////////////////////////////
			///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT ID FROM reports WHERE REPORTING_USERNAME = ? AND REPORTED_USERNAME = ? AND CONTENT = ?  LIMIT 1";

			$stmt1 = $pdo_conn_login->prepare($sql);

			$stmt1->execute(array($sender,$offender,$message));

			if(!$stmt1->rowCount()){
					
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "INSERT INTO reports (REPORTING_USERNAME,REPORTED_USERNAME,CONTENT,TIME) VALUES(?,?,?,?)";

				$stmt2 = $pdo_conn_login->prepare($sql);

				$stmt2->execute(array($sender,$offender,$message,$timesent));
										
		
				$subject =  $sender.' HAS FILED A REPORT AGAINST '.$offender;
				$message = $message;
				
			////////////////SEND PM AND EMAIL TO ALL ADMINS/////////////////////////////////////////////////							
				///////////PDO QUERY////////////////////////////////////	
				
				$sql = "SELECT EMAIL, USERNAME FROM members WHERE USER_PRIVILEGE='ADMIN'";

				$stmt3 = $pdo_conn_login->prepare($sql);

				$stmt3->execute(array());
				
				while($admin_rows = $stmt3->fetch(PDO::FETCH_ASSOC)){	

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
					
							 $to = $receiver_email;									 									 
										
										$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
													NOTE: This email was sent to you because you are part of the administrators team at ".$getdomain.". 
											  please kindly ignore this message if otherwise.\n\n\n please do not reply to this email.\n\n\nThank you";
											  
							 $headers="from: DoNotReply@".$domain_name."\r\n";
							 sendHTMLMail($to,$subject,$message,$footer,$headers);
							 

						
						
				}
				
				
			////////////////SEND PM AND EMAIL TO ALL MODS/////////////////////////////////////////////////							
				///////////PDO QUERY////////////////////////////////////	
				
				$sql = "SELECT EMAIL, USERNAME FROM members WHERE USER_PRIVILEGE='MODERATOR'";

				$stmt4 = $pdo_conn_login->prepare($sql);

				$stmt4->execute(array());
				
				while($mod_rows = $stmt4->fetch(PDO::FETCH_ASSOC)){	

						$receiverusername = $mod_rows["USERNAME"];
						$receiver_email = $mod_rows["EMAIL"];
						
					/////////////SEND MODS PM///////////////////////////////////
					///////////PDO QUERY////////////////////////////////////	
					/*	
						$sql = "INSERT INTO privatemessage (INBOX,TIME,SENDER,USERNAME,COPY_OF_INBOX,DATE,MESSAGE_SUBJECT) VALUES(?,?,?,?,?,?,?)";

						$stmt4 = $pdo_conn_login->prepare($sql);

						$stmt4->execute(array($message,$timesent,$sender,$receiverusername,$message,$datesent,$subject));		
					*/
					////////////SEND MODS EMAIL/////////////////////////////////////
					
							 $to = $receiver_email;									 									 
										
										$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
													NOTE: This email was sent to you because you are part of the moderators team at ".$getdomain.". 
											  please kindly ignore this message if otherwise.\n\n\n Please do not reply to this email.\n\n\nThank you";
											  
							 $headers="from: DoNotReply@".$domain_name."\r\n";
							 sendHTMLMail($to,$subject,$message,$footer,$headers);
							 
					///////////SEND THE REPORT SENDER EMAIL ONLY//////////////////////////////////////////
						
							///////////PDO QUERY////////////////////////////////////	
						
						$sql = "SELECT EMAIL FROM members WHERE USERNAME = ? LIMIT 1";

						$stmt5 = $pdo_conn_login->prepare($sql);

						$stmt5->execute(array($sender));
						
						$sender_row = $stmt5->fetch(PDO::FETCH_ASSOC);
							
							 $to = $sender_row["EMAIL"];
							 
							 $message = 'Hello '.$sender.'<br/>Thank you for contacting the '.$domain_name.' Teams<br/>
											Your Report has been received and it will be resolved shortly.<br/><br/>
											Details of your report are as follows:<br/>'.$message;
							 
							 $subject = 'REPORT TEAMS - '.$domain_name;
										
										$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
													NOTE: This email was sent to you because you submitted a report at ".$getdomain.". 
											  please kindly ignore this message if otherwise.\n\n\n Please do not reply to this email.\n\n\nThank you";
											  
							 $headers="from: DoNotReply@".$domain_name."\r\n";
							 sendHTMLMail($to,$subject,$message,$footer,$headers);
							 
				

						
						
				}
			
			}	


			$data= "<span class='green'>your report has been submitted successfully, it will be reviewed by us shortly.<br/>Thank You  </span>";
			
			////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
			echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($data)."')</script>";
																


		}

		else{
			
			$data= "<h2 class='red'>Fields marked(*) are required</h2>";	
			$asterix_all = "<span class=asterix>*</span>";

			if($message){
				
				$getmessage=$message;
			}
			
			if(!$message)
				$message_field_err = "field_err";
	
			if(!$offender && $message)
				$data = '<span>An unexpected error has occurred<br/>We are are sorry about that</span>';
				
		}

	}
	else{
	$not_logged="";

	$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

	}

}


?>


<!DOCTYPE HTML>
<html>
<head>
<title>MAKE REPORT</title>
<?php require_once("include-html-headers.php")   ?>
<script></script>

<style>
</style>
</head>

<body>
<div class="wrapper">

	<?php if(isset($_SESSION["username"])) require_once('euromenunav.php') ?>

	<span id="go_up"></span>
			
	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		$page_self = getReferringPage("qstr url");
		
			echo "<span class='blue'>Make Report</span> "  ;
			
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<?php echo getMidPageScroll(); ?>	
		<h1 class="h_bkg">REPORT ISSUES</h1>
		
		<div class="type_b">			

			<?php if(isset($not_logged))   echo $not_logged    ?>

			<ul>
				<li><?php if(isset($data))   echo '<div class="errors blink">'.$data.'</div>';   ?></li>
				<form action="make-report" method="post">					
					<fieldset>
						<label>Message:</label>
						<li><textarea  class="only_form_textarea_inputs <?= $message_field_err ?>"  placeholder="Type your message here" name="composedmessage"><?php if(isset($getmessage))  echo $getmessage ?></textarea><?php if(!$message) echo $asterix_all  ?></li>
						<li><input  type="hidden" value="<?php if(isset($offender)) echo $offender;  ?>" name="offender"></li>
					</fieldset>
					<li><input  class="formButtons"  type="submit" value="SUBMIT REPORT" name="make_report"></li>

				</form>
			</ul>
		</div>
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>
