<?php      

session_start();
require_once ("forumdb_conn.php");
require_once ("phpfunctions.php");

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();

date_default_timezone_set("Africa/Lagos");

$not_logged="";$credit_amt="";$other_credit_amt="";$action_srch="";$allowed="";$output="";$output_all="";
$options="";$option="";$priv_val_opt="";$susp_val_opt="";


$username = $_SESSION["username"];
	

if($username){
	
				
	//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////
	
	if(isset($_COOKIE["form_gate_response"])){
		
		$alert_user = $_COOKIE["form_gate_response"];
		
			
	/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

			setcookie("form_gate_response", "", (time() -  1800));

	}


/////////////////GET THE USER PRIVILEGE///////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	
			$privilege = getUserPrivilege($username);									
			
			if($privilege != "ADMIN" )
					$no_view_privilege = "<span class='red'> ".$username." Sorry You do not have enough Privilege to view this Page</span>";
	
	
					
	
}

else{

	header("location:login");
	exit();

}


?>





<!DOCTYPE HTML>
<html>
<head>
<title>INVESTMENT RETURN MATCH</title>
<?php require_once("include-html-headers.php")   ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>Investment Return Match</a> "  ;
			
	?>
	</header>



	<?php if($not_logged)   echo "<div class='view_user_wrapper'>".$not_logged."</div>";

	if(isset($no_view_privilege))   echo "<div class='view_user_wrapper'>".$no_view_privilege."</div>"; 

	?>

	<?php if($privilege == "ADMIN"){  ?>
		
		
	<div class="view_user_wrapper" id="hide_vuwbb">
			<?php getMidPageScroll(); ?>

		<h1 class="h_bkg" id="f1">INVESTMENT RETURN MATCH</h1>  
		<div class="type_a">
			
			<?php if(isset($alert_user)) echo $alert_user; ?>
			<br/><input type="button" class="formButtons start_btn" value="Authorize" />
				<span></span>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">INVESTMENT RETURN MATCH<span class="close_modal">&times;</span></div>			
					<div class="errors">ARE YOU SURE YOU WANT TO RUN THIS MATCHING</div>
					<form method="post" action="euro-secured-do-second-matching">
						<fieldset>							
							<label>AVN<span class="red">*</span></label>
							<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />													
						</fieldset>				
						<input class="formButtons" type="submit" name="manual_match" value="YES" />
						<input type="button"  class="formButtons close_modal" value="CANCEL" />
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