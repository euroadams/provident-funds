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

	if($_SESSION["username"]){
			
		$sender=$_SESSION['username'];
		$message=protect($_POST['composedmessage']);
		$timesent=time();


	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT ID,FULL_NAME FROM members WHERE USERNAME = ?  LIMIT 1";

		$stmt = $pdo_conn_login->prepare($sql);

		$stmt->execute(array($sender));
		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$full_name = $row["FULL_NAME"];
		
		if($message ){
					
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "SELECT ID FROM letters_of_happiness WHERE CONTENT LIKE ?  AND SENDER = ?  LIMIT 1";

				$stmt1 = $pdo_conn_login->prepare($sql);

				$stmt1->execute(array($message,$sender));
				
				if(!$stmt1->rowCount()){
					
					$loc = getLocation();
				
				///////////PDO QUERY////////////////////////////////////	
					
					$sql = "INSERT INTO letters_of_happiness (CONTENT,SENDER,FULL_NAME,LOCATION,TIME) VALUES(?,?,?,?,?)";

					$stmt3 = $pdo_conn_login->prepare($sql);

					$stmt3->execute(array($message,$sender,$full_name,$loc,$timesent));
					
					/**********CLEAR THE USER FOR NEXT TRANSACTION**************/
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "UPDATE members SET LOH_STATUS = 'CLEARED' WHERE USERNAME = ? LIMIT 1";
					$stmt4 = $pdo_conn_login->prepare($sql);

					$stmt4->execute(array($sender));
					
					
				}

			$data= "<span  class='green blink'>Thank You for taking out time to share your testimony.<br/>your letter of happiness shall be published soon on our  <a class='links' href='testimonials'>Testimonials</a> page</span>";
			
			////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
			echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($data)."')</script>";
				

			

		}

		else{
			
			$data= "<h4 class='red'>Fields marked(*) are required</h4>";	
			$asterix_all = "<span class=asterix>*</span>";

			if($message){
				
				$getmessage=$message;
			}
			
			
			if(!$message)
				$message_field_err = "field_err";
					
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
<title>WRITE TESTIMONIAL</title>
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

		echo "<a href='loh' title='Testimony'>Write Testimonial</a> "  ;
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>
		<h1 class="h_bkg">WRITE TESTIMONIAL</h1>
		
		<div class="type_b">
			<div class="errors" style="text-align:left;" >NOTE: YOUR TESTIMONIAL MUST INCLUDE ALL THE FOLLOWING:<br/>- PACKAGE NAME<br/>- YOUR INVESTMENT AMOUNT AND DATE<br/>- YOUR RETURN AMOUNT AND DATE</div>
			<?php if(isset($not_logged))   echo $not_logged    ?>

			<ul>
				<li><span id="pmsent"><?php if(isset($data))   echo $data    ?></span></li>
				<form action="loh" method="post">
					<fieldset>			
						<label>Message:</label>
						<li><textarea  class="only_form_textarea_inputs <?= $message_field_err ?>"  placeholder="Type your message here" name="composedmessage"><?php if(isset($getmessage))  echo $getmessage ?></textarea><?php if(!$message) echo $asterix_all  ?></li>
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
