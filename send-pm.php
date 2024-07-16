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

$username = $_SESSION["username"];

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$get_reply=""; $readonly="";$reply_to_user="";$asterix_all="";$subject_field_err="";$user_field_err="";
$message_field_err="";$receiverusername="";$messagesubject="";$message="";


$page_self = getReferringPage("qstr url");
//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////

if(isset($_COOKIE["form_gate_response"])){
	
	$data = $_COOKIE["form_gate_response"];
	
		
/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

		setcookie("form_gate_response", "", (time() -  1800));

}


if(isset($_GET["pm"]) ){
	
	
		$reply_id = $_GET["pm"];
		
///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT * FROM privatemessage WHERE ID= ?";

	$stmt0 = $pdo_conn_login->prepare($sql);

	$stmt0->execute(array($reply_id));
	
	if($stmt0->rowCount()){
		
	$reply_row = $stmt0->fetch(PDO::FETCH_ASSOC);
		
	$reply_to_user = $reply_row["SENDER"];
	
	$get_reply = str_ireplace("%%%***%%%", "", "RE: ".$reply_row["MESSAGE_SUBJECT"]);
	
	$get_reply = str_ireplace("***esc***", "", $get_reply);
	
	$get_reply = preg_replace("#\[a(.*)\](.*)\[/a\]#isU", "$2", $get_reply);
	
	$readonly="readonly";
	
	}
			
}


if(isset($_POST["get_reply"])){
	
	
	$get_reply=$_POST["get_reply"];
	
	if($get_reply )
	$readonly="readonly";
	
}

if(isset($_GET["cuser"])){
	
$reply_to_user = $_GET["cuser"];

if($reply_to_user)
	$readonly="readonly";

}

if(isset($_POST["r2user"])){
	
$reply_to_user=$_POST["r2user"];

if($reply_to_user)
	$readonly="readonly";

}

/*
if($reply_to_user == ""){
	
$reply_to_user=$_SESSION["username"];

if($reply_to_user != "" )
	$readonly="readonly";
	


}*/


if(isset($_POST["m2m_pm"]) || isset($_POST["receiver"])){
	
	if(isset($_POST["receiver"]))
		$getreceiver = $_POST["receiver"];
	$readonly = 'readonly';
	
}


////////////////////////ON SEND/////////////////////////////////////////////////////////////////////



if(isset($_POST['sendmessage'])){

		if($_SESSION["username"]){
			

		$sender=$_SESSION['username'];

		$receiverusername=protect($_POST['receiver']);
		$message=protect($_POST['composedmessage']);
		$messagesubject=protect($_POST['subject']);

		$messagesubject=protect($_POST['subject']);
		$timesent=time();
		$datesent=Date('Y-m-d h:i:s');

		$receiverusernamelc=strtolower($receiverusername);



		$senderlc=strtolower($sender);

		if($messagesubject && $message && $receiverusername){
			
		if($sender!=$receiverusername && $senderlc!=$receiverusernamelc){


		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT * FROM members WHERE USERNAME = ?";

			$stmt1 = $pdo_conn_login->prepare($sql);

			$stmt1->execute(array($receiverusername));
			
		$userexist= $stmt1->rowCount();

		if($userexist){
			
			//////////PREVENT REFRESH PAGE EFFECT////////////////////////////////////////////////////////
			///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT ID FROM privatemessage WHERE SENDER = ? AND USERNAME = ? AND INBOX = ? AND MESSAGE_SUBJECT = ? LIMIT 1";

			$stmt1 = $pdo_conn_login->prepare($sql);

			$stmt1->execute(array($sender,$receiverusername,$message,$messagesubject));

			if(!$stmt1->rowCount()){
					
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "INSERT INTO privatemessage (INBOX,TIME,SENDER,USERNAME,COPY_OF_INBOX,DATE,MESSAGE_SUBJECT) VALUES(?,?,?,?,?,?,?)";

				$stmt2 = $pdo_conn_login->prepare($sql);

				$stmt2->execute(array($message,$timesent,$sender,$receiverusername,$message,$datesent,$messagesubject));
			}	


			$data= "<span class='green'>your message has been sent to </span><span class='blue'>".$receiverusername."</span>";
								
			////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
			echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($data)."')</script>";
								
		}
		else{
			$data= "<span class='red'>Sorry the user: <span class=blue>".$receiverusername."</span> was not found<br/> Your message was not sent </span>";	
			
			if($message){
				$getmessage=$message;
			}
			
				if($messagesubject){
				$getsubject=$messagesubject;
			}
			
			
		}


		}


		else{
			
			$data= "<span class='red'>sorry you cannot send message to yourself</span>";	
			
				if($message){
					
				$getmessage=$message;
				
				}
			
				if($messagesubject){
					
				$getsubject=$messagesubject;
				
				}
			
			
			if($receiverusername){
				
				$getreceiver=$receiverusername;
				
				}
			
			
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
			
			
			if($receiverusername){
					
				$getreceiver=$receiverusername;
				
			}
			
			if(!$receiverusername)
				$user_field_err = "field_err";
			if(!$message)
				$message_field_err = "field_err";
			if(!$messagesubject)
				$subject_field_err = "field_err";
			
			
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
<title>SEND PM</title>
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

		//if(getUserPrivilege($username) == 'ADMIN' || getUserPrivilege($username) == 'MODERATOR')
			//echo "<a href='".$page_self."'>SEND PM</a> "  ;
		//else
			echo "<span class='blue'>SEND PM</span> "  ;
			
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<?php echo getMidPageScroll(); ?>
		<h1 class="h_bkg">SEND PM</h1>
		<div class="type_a">

			<?php if(isset($not_logged))   echo $not_logged    ?>

			<ul>
				<li><?php if(isset($data))   echo '<div class="errors blink">'.$data.'</div>';   ?></li>
				<form action="send-pm" method="post">
					<fieldset>
						
						<label>Receiver:</label>
						<li><input maxlength="30"   class="only_form_textarea_inputs <?= $user_field_err ?>"  <?php if($readonly) echo $readonly; ?>     placeholder="Type in the name of the receiver here" type="text"  name="receiver" value="<?php if(isset($getreceiver) && $reply_to_user=="") echo $getreceiver;  if(isset($reply_to_user)) echo $reply_to_user;  ?>"><?php  if(!$receiverusername) echo $asterix_all  ?></li>
						<label>Subject:</label>
						<li><input maxlength="100"  class="only_form_textarea_inputs <?= $subject_field_err ?>" type="text"  placeholder="Type in your message subject here " name="subject" value="<?php if(isset($getsubject)) echo $getsubject ;    if(isset($get_reply) && !isset($getsubject)) echo $get_reply ?>"><?php  if(!$messagesubject )echo $asterix_all  ?></li>
						<label>Message:</label>
						<li><textarea  class="only_form_textarea_inputs <?= $message_field_err ?>"  placeholder="Type your message here" name="composedmessage"><?php if(isset($getmessage))  echo $getmessage ?></textarea><?php if(!$message) echo $asterix_all  ?></li>
						<li><input  type="hidden" value="<?php echo $reply_to_user  ?>" name="r2user"></li>
						<li><input  type="hidden" value="<?php echo $get_reply  ?>" name="get_reply"></li>
					</fieldset>
					<li><input id="sendpm"  class="formButtons"  type="submit" value="SEND" name="sendmessage"></li>

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
