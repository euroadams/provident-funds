<?php


session_start();
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
$message_field_err="";$receiverusername="";$messagesubject="";$message="";$hid="";



$page_self = getReferringPage("qstr url");
//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////

if(isset($_COOKIE["form_gate_response"])){
	
	$data = $_COOKIE["form_gate_response"];
	
		
/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

		setcookie("form_gate_response", "", (time() -  1800));

}



$username = $_SESSION["username"];

if(getUserPrivilege($username) == 'ADMIN' || getUserPrivilege($username) == 'MODERATOR'){

	if($username){	

		if(!isset($_POST["hid"]) && !isset($_POST["eid"])){
			header("location:support-teams"); 
			exit();
		} 
		/**********CAPTURE HID(HELP ID) PASSED************************/

		if(isset($_POST["hid"]))
			$hid = protect($_POST["hid"]);


		/*********FETCH DETAILS FROM HID PASSED****************************************/
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT TICKET_NO,SUBJECT FROM help WHERE ID = ? LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);

			$stmt->execute(array($hid));
			
			$t_rows = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$ticket_num = $t_rows["TICKET_NO"];
			$ticket_subj = 'RE: '.$t_rows["SUBJECT"];
			
		
		
		/**********CAPTURE EDIT ID eid PASSED************************/

		if(isset($_POST["eid"])){
			
			$eid = protect($_POST["eid"]);


		/*********FETCH DETAILS FROM EID PASSED****************************************/
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT SUBJECT,REPLY_CONTENT,TICKET_NO FROM support_replies WHERE ID = ? LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);

			$stmt->execute(array($eid));
			
			$e_rows = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$ticket_num = $e_rows["TICKET_NO"];
			$messagesubject = $e_rows["SUBJECT"];
			$message = $e_rows["REPLY_CONTENT"];

			/******************ON EDIT***************************************/		
			
			if(isset($_POST["editmessage"])){
				
				$message=protect($_POST['composedmessage']);
				$messagesubject=protect($_POST['subject']);
				
				if($message && $messagesubject ){
					
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "UPDATE support_replies SET SUBJECT = ?, REPLY_CONTENT = ? WHERE ID = ? LIMIT 1";

					$stmt = $pdo_conn_login->prepare($sql);

					if($stmt->execute(array($messagesubject,$message,$eid)))
						$data = "<span class='green'>YOUR MODIFICATIONS HAS BEEN SAVED FOR TICKET:".$ticket_num."</span>";
											
						////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
						echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($data)."')</script>";
									

					
				}				
				else{
					
					$data= "<span class='red'>Fields marked(*) are required</span>";	
					$asterix_all = "<span class=asterix>*</span>";
					
					if(!$message)
						$message_field_err = "field_err";
					if(!$messagesubject)
						$subject_field_err = "field_err";
					
							
				}
				
			}
			
		}

	////////////////////////ON SEND/////////////////////////////////////////////////////////////////////

		if(isset($_POST['sendmessage'])){
				
			$sender=$username;	
			$message=protect($_POST['composedmessage']);
			$messagesubject=protect($_POST['subject']);
			$timesent=time();

			if($messagesubject && $message && $sender ){	
						
					///////////PDO QUERY////////////////////////////////////	
						
						$sql = "SELECT ID FROM support_replies WHERE SUBJECT LIKE ? AND REPLY_CONTENT LIKE ? AND TICKET_NO LIKE ? AND SENDER = ?  LIMIT 1";

						$stmt1 = $pdo_conn_login->prepare($sql);
						$stmt1->execute(array($messagesubject,$message,$ticket_num,$sender));
						
						if(!$stmt1->rowCount()){					
						
						///////////PDO QUERY////////////////////////////////////	
							
							$sql = "INSERT INTO support_replies (SUBJECT,REPLY_CONTENT,SENDER,TIME,TICKET_NO,HID) VALUES(?,?,?,?,?,?)";

							$stmt2 = $pdo_conn_login->prepare($sql);

							if($stmt2->execute(array($messagesubject,$message,$sender,$timesent,$ticket_num,$hid))){
								
									
							///////////PDO QUERY////////////////////////////////////	
								
								$sql = "UPDATE help SET TOTAL_REPLIES = (TOTAL_REPLIES + 1) WHERE ID = ?";

								$stmt3 = $pdo_conn_login->prepare($sql);

								$stmt3->execute(array($hid));
								
								
							/////////SEND RESPOND EMAIL TO THE CONTACTOR/////////////////////////////////////////////////////////////	
							///////////PDO QUERY////////////////////////////////////	
								
								$sql = "SELECT SENDER FROM help WHERE ID = ?";

								$stmt4 = $pdo_conn_login->prepare($sql);

								$stmt4->execute(array($hid));
								$cnt_row = $stmt4->fetch(PDO::FETCH_ASSOC);
								$cont_user = $cnt_row["SENDER"];
								
								$to = getMemberEmail($cont_user)									;							
								 
								 $message = 'Hello '.$cont_user.'<br/>Thank you for contacting the '.$domain_name.' Support Teams<br/>
												<h2 style="color:blue;">Your support Request with ticket number: '.$ticket_num.' has been resolved.<h2/><br/>'.$message;
																							
								 
								 $subject = 'SUPPORT TEAMS - '.$domain_name.'(TICKET:'.$ticket_num.')';
											
											$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
														NOTE: This email was sent to you because you submitted a support ticket at ".$getdomain.". 
												  please kindly ignore this message if otherwise.\n\n\n Please do not reply to this email.\n\n\nThank you";
												  
								 $headers="from: DoNotReply@".$domain_name."\r\n";
								 sendHTMLMail($to,$subject,$message,$footer,$headers);
								
									
							}
								
						}

						$data = "<span class='green'>YOUR RESPOND HAS BE SENT TO TICKET:".$ticket_num."</span>";
										

				////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
				echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($data)."')</script>";
					
			}

			else{
				
				$data= "<span class='red'>Fields marked(*) are required</span>";	
				$asterix_all = "<span class=asterix>*</span>";
				
				if(!$message)
					$message_field_err = "field_err";
				if(!$messagesubject)
					$subject_field_err = "field_err";
						
			}


		}

	}
	else{

	$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

	}

}
?>


<!DOCTYPE HTML>
<html>
<head>
<title>REPLY TICKETS</title>
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

		echo "<a href='reply-tickets' title=>Reply Ticket</a> "  ;
				
		?>
	</header>
	<?php if(getUserPrivilege($username) == 'ADMIN' || getUserPrivilege($username) == 'MODERATOR'){ ?>
	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper">

		<?php echo getMidPageScroll(); ?>
	
		<?php if(isset($not_logged))   echo $not_logged    ?>

		<ul>
			<?php if(isset($data))   echo '<div class="blink errors">'.$data.'</div>';   ?>
			<h2 class="cyan">SUPPORT TICKET REPLY</h2>
			<h3 class="blue">TICKET NUMBER: <?php if(isset($ticket_num)) echo $ticket_num ; ?> </h3>
			<form action="reply-tickets" method="post">
				<fieldset>
					<label>Subject<span class="red">*</span></label>
					<li><input maxlength="100"  class="only_form_textarea_inputs <?= $subject_field_err ?>" type="text"  placeholder="Type in your message subject here " name="subject" value="<?php if(isset($messagesubject)) echo $messagesubject ; if(!$messagesubject) echo $ticket_subj; ?>"><?php  if(!$messagesubject )echo $asterix_all  ?></li>
					<label>Message<span class="red">*</span></label>
					<li><textarea  class="only_form_textarea_inputs <?= $message_field_err ?>"  placeholder="Type your message here" name="composedmessage"><?php if(isset($message))  echo $message ?></textarea><?php if(!$message) echo $asterix_all  ?></li>										
				</fieldset>	
					<?php if($hid){  ?>
						<input type="hidden" name="hid" value="<?php if(isset($hid)) echo $hid;  ?>" />
						<li><input  class="formButtons"  type="submit" value="SUBMIT" name="sendmessage"></li>
					<?php }else if(isset($eid) && $eid){ ?>
						<input type="hidden" name="eid" value="<?php if(isset($eid)) echo $eid;  ?>" />
						<li><input  class="formButtons"  type="submit" value="UPDATE" name="editmessage"></li>
					<?php } ?>
			</form>
		</ul>
	</div>
	<?php }else{ echo '<div class="view_user_wrapper" id="hide_vuwbb"><h2 class="red">Sorry you do not have enough privilege to view this page!!!</h2></div>';}  ?>
	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>-->
	<span id="go_down"></span>

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>



