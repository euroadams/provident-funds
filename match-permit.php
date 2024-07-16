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
	
	
		
	$page_self = getReferringPage("qstr url");
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
	
	
	
	if($privilege == "ADMIN"){ 
 
/////////////////////////////////////////IF MATCH_PERMIT IS SET/////////////////////////////
		if(isset($_POST["match_permit"])){
			
			/*******MAKE SURE A MATCH TYPE IS SELECTED***************/			
			if(!empty($_POST["match_type"])){
				
				$mtype_qry="";
				$avn = protect($_POST["avn"]);
				$perm = $_POST["perm"];
				$match_type = $_POST["match_type"];
				$match_type_str = implode("<br/>", $match_type);
				
				for($idx=0; $idx <= (count($match_type) - 1); $idx++){
					
					if($idx == 0){
						if($match_type[$idx] == "FIRST MATCHING(CAPITAL RETURN)")
							$mtype_qry = " MATCH_TYPE = 'FIRST_MATCHING' ";
						elseif($match_type[$idx] == "SECOND MATCHING(INVESTMENT RETURN)")
							$mtype_qry = " MATCH_TYPE = 'SECOND_MATCHING' ";
					}
						
					if($idx == 1){
						if($match_type[$idx] == "FIRST MATCHING(CAPITAL RETURN)")
							$mtype_qry .= " OR MATCH_TYPE = 'FIRST_MATCHING' ";
						elseif($match_type[$idx] == "SECOND MATCHING(INVESTMENT RETURN)")
							$mtype_qry .= " OR MATCH_TYPE = 'SECOND_MATCHING' ";
					}
										
				}

				if(verifyAVN($username,$avn)){
					
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "UPDATE matching_permit SET MATCH_PERMISSION = ? WHERE ".$mtype_qry;

					$stmt = $pdo_conn_login->prepare($sql);

					$stmt->execute(array($perm));
					
					$alert_user = '<span class="red">PERMISSION TO BEGIN MATCHING  HAS BEEN SUCCESSFULLY '.$perm.' FOR THE FOLLOWING MATCH TYPES:<br/>'.$match_type_str.'</span>';
					
					////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
					echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert_user)."')</script>";
						

														
					
				}
				else
					$alert_user = '<span class=" errors blink">SORRY AVN VERIFICATION FAILED</span>';
			
			}
			 else
				$alert_user = '<span class="red">Please select the target match type. </span>';
			
				
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
<title>MATCH PERMIT</title>
<?php require_once("include-html-headers.php")   ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>Match Permit</a> "  ;
			
	?>
	</header>



	<?php if($not_logged)   echo "<div class='view_user_wrapper'>".$not_logged."</div>";

	if(isset($no_view_privilege))   echo "<div class='view_user_wrapper'>".$no_view_privilege."</div>"; 

	?>

	<?php if($privilege == "ADMIN"){  ?>
		
		
	<div class="view_user_wrapper" id="hide_vuwbb">
			<?php getMidPageScroll(); ?>

		<h1 class="h_bkg" id="f1">MATCH PERMIT</h1>  
		<div class="type_a">
			
			<?php if(isset($alert_user)) echo $alert_user; ?>
			<br/><input type="button" class="formButtons start_btn" value="Authorize" />
				<span></span>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">MATCH PERMIT<span class="close_modal">&times;</span></div>			
					<form method="post" action="match-permit">
						<fieldset>
							<label>MATCH TYPE:</label>
							<select required name="match_type[]" multiple class="only_form_textarea_inputs">
								<option>FIRST MATCHING(CAPITAL RETURN)</option>
								<option>SECOND MATCHING(INVESTMENT RETURN)</option>
							</select>
							<label>PERMISSION:</label>		
							<select name="perm" class="only_form_textarea_inputs">				
									<option <?php if(isset($perm) && $perm == "GRANTED") echo 'selected'; ?> >GRANTED</option>
									<option <?php if(isset($perm) && $perm == "DENIED") echo 'selected'; ?> >DENIED</option>
							</select>
							<label>AVN<span class="red">*</span></label>
							<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />						
						</fieldset>				
						<input class="formButtons" type="submit" name="match_permit" value="OK" />
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