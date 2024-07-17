<?php  
 
session_start();
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
	 
			if(isset($_POST["run_query"])){
				
				$avn = protect($_POST["avn"]);
				
				if(verifyAVN($username,$avn)){
											
							
					//////DEFINE ARRAY OF PACKAGES SO YOU CAN LOOP THROUGH ALL PACKAGES AND DO QUERY///////////////////////////////////
						
					$package_arr = array("ultimate","royal","master","lord","elite","premium","classic","standard");

					//////////////LOOP THROUGH EACH PACKAGES ////////////////////////////////////
					
					
					/*********SET RECYCLING DEADLINE******************/
						$recyl_deadline = getRecyclingDeadline();
					
					foreach($package_arr as $pack_name){
						
						$donation_table = 'euro_'.$pack_name.'_donations';
						$matching_table = 'euro_'.$pack_name.'_matching';
																		
						///////////PDO QUERY////////////////////////////////////	
						$sql="";
						
						/*******MULTI LOOP QUERIES************************/						
						
						//$sql = "ALTER TABLE ".$donation_table." ADD PT_REMATCH_TIME INT NOT NULL AFTER TRANS_NUMBER  ";
						//$sql = "ALTER TABLE ".$donation_table." DROP PT_REMATCH_TIME ";
						//$sql = "ALTER TABLE ".$matching_table." CHANGE CONFIRMED CONFIRMED VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'PENDING'";								
						//$sql = "ALTER TABLE members ADD LOH_STATUS VARCHAR(50) NOT NULL DEFAULT 'CLEARED' AFTER LOOP_STATUS";

						$recyl_deadline = (time() + 518400);
						/*******SINGLE LOOP QUERIES************************/						
						////////////ENSURE TO SET BREAK FOR SINGLE LOOP QUERIES/////////////////////						
						$sql = "UPDATE members SET  COMMENT1='', COMMENT2='', SUSPENSION_STATUS='NO', RECYCLING_DEADLINE=? ";
						//$sql = "UPDATE members SET CURRENT_PACKAGE='NONE', FLOW_DIRECTION='NONE', LOOP_STATUS='COMPLETE', TOTAL_DECL=0, TOTAL_PURGE=0, COMMENT1='', COMMENT2='', SUSPENSION_STATUS='NO', RECYCLING_DEADLINE=?  WHERE USERNAME NOT IN('seer','west','izzy') ";
						//$sql = "UPDATE members SET CURRENT_PACKAGE='NONE', SUSPENSION_STATUS='NO',  FLOW_DIRECTION='NONE', LOOP_STATUS='COMPLETE', TOTAL_DECL=0, TOTAL_PURGE=0, COMMENT1='', COMMENT2='', RECYCLING_DEADLINE=?  WHERE CURRENT_PACKAGE IN ('CLASSIC','PREMIUM','ELITE','NONE') ";
						/*$sql = "ALTER TABLE transactions ADD DONATION1 VARCHAR(50) NOT NULL DEFAULT 'PENDING' AFTER STATUS, 
								ADD DONATION1_TIME INT NOT NULL AFTER DONATION1, ADD DONATION2 VARCHAR(50) NOT NULL DEFAULT 'PENDING' AFTER DONATION1_TIME,
								ADD DONATION2_TIME INT NOT NULL AFTER DONATION2";*/
						
						$break_loop = true;////COMMENT OUT FOR MULTI LOOP////////////
																

						if($sql){
								
							$stmt = $pdo_conn_login->prepare($sql);
							if($stmt->execute(array($recyl_deadline)))
								$alert = '<div id="green" class="errors blink">YOUR QUERY HAS BEEN EXECUTED SUCCESSFULLY!!!</div>';
							else
								$alert = '<div  class="errors blink">YOUR QUERY EXECUTION HAS FAILED!!!</div>';
								
						}
						else
							$alert = '<div  class="errors blink">SORRY QUERY EXECUTION HAS BEEN DISABLED!!!</div>';
						
						if(isset($break_loop))
							break;
						
					}
					
				}
				else{
						$alert = '<div class="errors blink">INCORRECT ACCOUNT VERIFICATION NUMBER(AVN),<br/> Please try again</div>';
				}
				
				
				////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
				echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert)."')</script>";
					
				
				
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
<title>QUERY MANAGER</title>
<?php require_once("include-html-headers.php")   ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>Query Manager</a> "  ;
			
	?>
	</header>



	<?php if(isset($not_logged))   echo "<div class='view_user_wrapper'>".$not_logged."</div>";

	if(isset($no_view_privilege))   echo "<div class='view_user_wrapper'>".$no_view_privilege."</div>"; 

	?>

	<?php if($privilege == "ADMIN"){  ?>
		
		
	<div class="view_user_wrapper" id="hide_vuwbb">
			<?php getMidPageScroll(); ?>

		<h1 class="h_bkg">QUERY MANAGER</h1>
		
		<div class="type_a">
		
			<?php if(isset($alert)) echo $alert; ?>
		
			<input class="formButtons  start_btn" type="button" value="RUN" />
		
			<div class="elt_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">QUERY EXECUTION<span class="close_modal">&times;</span></div>			
					<div class="errors">RUNNING  A WRONG QUERY MAY HARM YOUR DATABASE<br/>ARE YOU SURE YOU STILL WANT TO RUN THIS QUERY</div>
					<form method="post" action="euro-secured-query-page">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<br/><input type="submit" name="run_query" class="formButtons" value="YES" />
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