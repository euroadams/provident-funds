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

setPageTimeZone();

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$get_reply=""; $readonly="";$reply_to_user="";$asterix_all="";$subject_field_err="";$user_field_err="";
$uname_field_err="";
$message_field_err="";$receiverusername="";$messagesubject="";$message="";

			
$page_self = getReferringPage("qstr url");
//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////

if(isset($_COOKIE["form_gate_response"])){
	
	$data = $_COOKIE["form_gate_response"];
	
		
/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

		setcookie("form_gate_response", "", (time() -  1800));

}
															

////////////////////////ON SEND/////////////////////////////////////////////////////////////////////

if(isset($_POST['sendmessage'])){
			
		$sender=$_SESSION['username'];
		if(isset($_POST["uname"]))
			$uname = protect($_POST["uname"]);
		if(!$sender)
			$sender = $uname;
		
		$message=protect($_POST['composedmessage']);
		$messagesubject=protect($_POST['subject']);
		$timesent=time();
		$datesent=Date('Y-m-d h:i:s');

		if($messagesubject && $message && $sender ){			
	
			////////////FIRST GET THE USER ID FROM MEMBERS TABLE FOR GENERATING TICKET NO/////////////////////////////////////////////////////
				
				/////////PDO QUERY////////////////////////////////////

				$sql = "SELECT ID FROM members WHERE USERNAME = ? LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($sender));
				
				if($stmt->rowCount()){
				
					$user_row = $stmt->fetch(PDO::FETCH_ASSOC);					
					$user_id = $user_row["ID"];				
					
					$ticket_num = generateFLRand("15",$user_id);
					
				///////////PDO QUERY////////////////////////////////////	
					
					$sql = "SELECT ID FROM help WHERE SUBJECT LIKE ? AND CONTENT LIKE ?  AND SENDER = ?  LIMIT 1";

					$stmt1 = $pdo_conn_login->prepare($sql);

					$stmt1->execute(array($messagesubject,$message,$sender));
					
					if(!$stmt1->rowCount()){					
					
					///////////PDO QUERY////////////////////////////////////	
						
						$sql = "INSERT INTO help (SUBJECT,CONTENT,SENDER,TIME,TICKET_NO) VALUES(?,?,?,?,?)";

						$stmt2 = $pdo_conn_login->prepare($sql);

						$stmt2->execute(array($messagesubject,$message,$sender,$timesent,$ticket_num));
						
						
						$subject = 'SUPPORT REQUEST BY '.$sender.': '.$messagesubject;
						$message = 'TICKET NUMBER:'.$ticket_num.'<hr/>'.$message;
						
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
									 

								
								
						}
						
						///////////SEND THE CONTACTING SENDER EMAIL ONLY//////////////////////////////////////////
						
							///////////PDO QUERY////////////////////////////////////	
						
						$sql = "SELECT EMAIL FROM members WHERE USERNAME = ? LIMIT 1";

						$stmt5 = $pdo_conn_login->prepare($sql);

						$stmt5->execute(array($sender));
						
						$sender_row = $stmt5->fetch(PDO::FETCH_ASSOC);
							
							 $to = $sender_row["EMAIL"];
							 
							 $message = 'Hello '.$sender.'<br/>Thank you for contacting the '.$domain_name.' Teams<br/>
											Your Request has been received and it will be resolved shortly.<br/><br/>
											Details of your request are as follows:<br/>'.$message;
							 
							 $subject = 'SUPPORT TEAMS - '.$domain_name;
										
										$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
													NOTE: This email was sent to you because you submitted a support ticket at ".$getdomain.". 
											  please kindly ignore this message if otherwise.\n\n\n Please do not reply to this email.\n\n\nThank you";
											  
							 $headers="from: DoNotReply@".$domain_name."\r\n";
							 sendHTMLMail($to,$subject,$message,$footer,$headers);
							 
				
						
					}

					$data = "<span class='green'>Thank You for contacting Us.<br/>your message has been dispatched to the <a class='links' href='".$getdomain."'>".$domain_name."</a> support teams. They will review it and get back to you shortly.<br/> </span>";
				
					////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
					echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($data)."')</script>";
																					

				
				}
				else{
					
					$data = "<span class='red'>Sorry the username:<span class='blue'>".$sender."</span> was not found<br/>Please enter your correct username to enable the support teams resolve your case swiftly<br/>Thank You.</span>";
				}
			

		}

		else{
			
			$data= "<span class='red'>Fields marked(*) are required</span>";	
			$asterix_all = "<span class=asterix>*</span>";

			if($message){
				
				$getmessage=$message;
			}
			
			if($messagesubject){
					
				$getsubject=$messagesubject;
			}
			
			if(!$message)
				$message_field_err = "field_err";
			if(!$messagesubject)
				$subject_field_err = "field_err";
			if(!$sender)
				$uname_field_err = "field_err";
					
		}


}
?>


<!DOCTYPE HTML>
<html>
<head>
<title>CONTACT SUPPORT</title>
<?php require_once("include-html-headers.php")   ?>
<script></script>

<style>
</style>
</head>

<body>
<div class="wrapper">

	<?php require_once('euromenunav.php') ?>

	<span id="go_up"></span>
			
	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		$page_self = getReferringPage("qstr url");

		echo "<a href='contact-support' title=>Contact Support</a> "  ;
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>
		<h1 class="h_bkg">CREATE A SUPPORT TICKET</h1>
		<div class="type_b">
			
			<?php if(isset($not_logged))   echo $not_logged    ?>

			<ul>
				<?php if(isset($data))   echo '<div class="blink errors">'.$data.'</div>';   ?>
				
				<form action="contact-support" method="post">
					<fieldset>
						<?php if(!$_SESSION["username"]){ ?>
							<label>Username<span class="red">*</span></label>
							<li><input  class="only_form_textarea_inputs <?= $uname_field_err ?>" type="text"  placeholder="Type your Username here" name="uname" value="<?php if(isset($uname)) echo $uname ;  ?>"><?php  if(isset($uname) && !$uname )echo $asterix_all  ?></li>
						<?php } ?>
						<label>Subject<span class="red">*</span></label>
						<li><input maxlength="100"  class="only_form_textarea_inputs <?= $subject_field_err ?>" type="text"  placeholder="Type in your message subject here " name="subject" value="<?php if(isset($messagesubject)) echo $messagesubject ;  ?>"><?php  if(!$messagesubject )echo $asterix_all  ?></li>
						<label>Message<span class="red">*</span></label>
						<li><textarea  class="only_form_textarea_inputs <?= $message_field_err ?>"  placeholder="Type your message here" name="composedmessage"><?php if(isset($message))  echo $message ?></textarea><?php if(!$message) echo $asterix_all  ?></li>
					</fieldset>	
						<li><input  class="formButtons"  type="submit" value="SEND" name="sendmessage"></li>
					
				</form>
			</ul>
		</div>
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>-->
	<span id="go_down"></span>

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>



