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

if($username){
	
	
	
$page_self = getReferringPage("qstr url");
//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////

if(isset($_COOKIE["form_gate_response"])){
	
	$alert = $_COOKIE["form_gate_response"];
	
		
/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

		setcookie("form_gate_response", "", (time() -  1800));

}

								 
	
/////////////////GET THE USER PRIVILEGE///////////////////////////////////////////////////////////////////////////////////////////////////////////	
	

	$privilege = getUserPrivilege($username);
				
	
	if($privilege != "ADMIN" )
			$no_view_privilege = "<span class='red'> ".$username." Sorry You do not have enough Privilege to view this Page</span>";

	 if($privilege == "ADMIN"){ 
	 
			if(isset($_POST["reset_avn"])){
				
				$target_user = protect($_POST["target_user"]);
				$avn = protect($_POST["avn"]);
				
				if(verifyAVN($username,$avn)){									

					////////////////////PDO QUERY////////////////////////////////////

					$sql =  "SELECT ID,EMAIL FROM members WHERE USERNAME=? LIMIT 1";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array($target_user));
					
					if($stmt->rowCount()){
							
						$id_row = $stmt->fetch(PDO::FETCH_OBJ);

						$id = $id_row->ID;
						$email = $id_row->EMAIL;

						$avn = generateFLRand("6",$id);
						$avn_enc = sha1($avn);
						
						////////////////////PDO QUERY////////////////////////////////////	
						$sql =  "UPDATE members SET AVN=?, OT_AVN=? WHERE USERNAME=? LIMIT 1";
						$stmt1 = $pdo_conn_login->prepare($sql);
						if($stmt1->execute(array($avn_enc,$avn,$target_user))){
						
							///SEND CONFIRMATION EMAIL TO USER////////////

							$to=$email;
							 $subject="AVN RESET NOTIFICATION@".$getdomain;
							 $message="Hello ".$target_user."\n Owing to your request to reset your AVN, The system has generated a new AVN for you.\n\n<div style='font-size:20px;color:#0000ff;'>Your New ACCOUNT VERIFICATION NUMBER(AVN) is:\n".$avn."</div>\n Please copy it and keep it save because you will need it for all your transactions.\nThank you\n\n\n\n";
										
							 $footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ."  All Rights Reserved.
											NOTE: This email was sent to you because you requested an AVN reset at ".$getdomain." . If you did not make such request, please kindly ignore this message.\n\n\n please do not reply to this email.";
							 $headers="from: DoNotReply@".$domain_name."\r\n";
							 sendHTMLMail($to,$subject,$message,$footer,$headers);
						
						
							$alert = '<div id="green" class="errors ">AVN RESET FOR <span class="cyan">'.$target_user.'</span> WAS SUCCESSFUL</div>';
							
						}
						
				
						////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
						echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert)."')</script>";
							
						 
						
					}else{
						$alert = '<div class="errors ">SORRY THE TARGET USER: <span class="cyan">'.$target_user.'</span> WAS NOT FOUND</div>';
					}	
					
					
				}
				else{
						$alert = '<div class="errors blink">INCORRECT ACCOUNT VERIFICATION NUMBER(AVN),<br/> Please try again</div>';
				}

				
			}
		
	}


}
else{
		header("location:login");
		exit();
}
 
 ?>
 
 
 
<!DOCTYPE HTML>
<html>
<head>
<title>AVN RESET</title>
<?php require_once("include-html-headers.php")   ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>AVN Reset</a> "  ;
			
	?>
	</header>



	<?php if(isset($not_logged))   echo "<div class='view_user_wrapper'>".$not_logged."</div>";

	if(isset($no_view_privilege))   echo "<div class='view_user_wrapper'>".$no_view_privilege."</div>"; 

	?>

	<?php if($privilege == "ADMIN"){  ?>
		
		
	<div class="view_user_wrapper" id="hide_vuwbb">
			
			<?php getMidPageScroll(); ?>

		<h1 class="h_bkg">AVN RESET</h1>
		<div class="type_a">	
		
		<?php if(isset($alert)) echo $alert; ?>
				
			<input class="formButtons  start_btn" type="button" value="RESET AVN" />		
			<div class="elt_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">AVN RESET<span class="close_modal">&times;</span></div>			
					<div class="errors">ATTENTION!!!<br/>ADMIN YOU ARE ABOUT TO RESET A MEMBER'S AVN<br/> ARE YOU SURE YOU WANT TO DO THIS?</div>
					<form method="post" action="avn-reset">
						<label>Target Member<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter the target member's username here" type="text" name="target_user" value="" class="only_form_textarea_inputs" />
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<br/><input type="submit" name="reset_avn" class="formButtons" value="YES" />
						<input type="button"  class="formButtons close_modal" value="NO" />
					</form>
				</div>
			</div>			
		</div>
	</div>
	

	<?php } ?>

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>