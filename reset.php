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

	$packages_option="";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$username = $_SESSION["username"];

if(getUserPrivilege($username) == 'ADMIN'){

	if($username){	
	
	
		
	$page_self = getReferringPage("qstr url");
	//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////
	
	if(isset($_COOKIE["form_gate_response"])){
		
		$alert = $_COOKIE["form_gate_response"];
		
			
	/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

			setcookie("form_gate_response", "", (time() -  1800));

	}

		
		
/******************GET PACKAGES********************/	

		////////////////////PDO QUERY////////////////////	

		$sql = "SELECT PACKAGE FROM packages ORDER  BY PACKAGE";

		$stmt1 = $pdo_conn->prepare($sql);
		$stmt1->execute();

		while($pack_row = $stmt1->fetch(PDO::FETCH_ASSOC)){
			
			$packages_option .= '<option>'.$pack_row["PACKAGE"].'</option>';
		}


	
		if(isset($_POST["reset_ph"])){
			
			$avn = $_POST["avn"];
			$package = $_POST["package"];
			
			if(verifyAVN($username,$avn)){
				
				
				//////DEFINE ARRAY OF PACKAGES SO YOU CAN LOOP THROUGH ALL PACKAGES AND EMPTY///////////////////////////////////
					
				//$package_arr = array("ultimate","royal","master","lord","elite","premium","classic","standard");


				//////////////LOOP THROUGH EACH PACKAGES////////////////////////////////////
				
				//foreach($package_arr as $pack_name){
					
					//$donation_table = 'euro_'.$pack_name.'_donations';
					//$matching_table = 'euro_'.$pack_name.'_matching';
					
					
					$donation_table = 'euro_'.strtolower($package).'_donations';
					$matching_table = 'euro_'.strtolower($package).'_matching';
					
						
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "TRUNCATE ".$donation_table;
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array());
					
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "TRUNCATE ".$matching_table;
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array());
						
					
				//}
				
				/*
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "TRUNCATE purges";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array());
					
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "TRUNCATE declinations";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array());
					
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "TRUNCATE transactions";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array());*/
				

				/////SET RECYCLING DEADLINE///////
				$recyl_deadline = getRecyclingDeadline();
				
				///////////PDO QUERY////////////////////////////////////	
				
				/*$sql = "UPDATE members SET CURRENT_PACKAGE = 'NONE',FLOW_DIRECTION='NONE',
						RECYCLING_DEADLINE=?,LOOP_STATUS = 'COMPLETE',
						TOTAL_PURGE=0,TOTAL_DECL=0";*/
				
				$sql = "UPDATE members SET CURRENT_PACKAGE = 'NONE',FLOW_DIRECTION='NONE',
						RECYCLING_DEADLINE=?,LOOP_STATUS = 'COMPLETE' WHERE CURRENT_PACKAGE = ?";

				$stmt = $pdo_conn_login->prepare($sql);

				if($stmt->execute(array($recyl_deadline,$package)))
					$alert = '<span class=" errors blink">ALL PH HAS BEEN SUCCESSFULLY RESET</span>';
				
				$alert = '<span id="green" class=" errors blink">THE '.$package.' PACKAGE HAS BEEN SUCCESSFULLY RESET</span>';
				
			}else{
				$alert = '<span class=" errors blink">SORRY AVN VERIFICATION FAILED</span>';
			}
			
						
				////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
				echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert)."')</script>";
					
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
<title>PACKAGE RESET</title>
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

		echo "<a href='reset' title=>Package Reset</a> "  ;
				
		?>
	</header>
	<?php if(getUserPrivilege($username) == 'ADMIN'){ ?>
	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>
		<h1 class="h_bkg">PACKAGE RESET</h1>
		<div class="type_a">
			<?php 
				if(isset($not_logged))   echo $not_logged;  
			
				 if(isset($alert))   echo $alert;  
			?>

			<ul>
				<input type="button" class="formButtons start_btn" value="PACKAGE RESET" />
				<span></span>
				<div class="modal">																						
					<div class="modal_content">
						<div class="modal_header clear">PACKAGE RESET<span class="close_modal">&times;</span></div>			
						<form action="reset" method="post">
							<label>AVN<span class="red">*</span></label>
							<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />						
							<label>PACKAGE<span class="red">*</span></label>
							<select required  name="package"  class="only_form_textarea_inputs" ><?php if($packages_option) echo $packages_option; ?></select><br/>
							<input type="submit" name="reset_ph" class="formButtons" " value="OK" />
						</form>
					</div>
				</div>			
			</ul>
		</div>
	</div>
	<?php }else{ echo '<div class="view_user_wrapper" id="hide_vuwbb"><h2 class="red">Sorry you do not have enough privilege to view this page!!!</h2></div>';}  ?>
	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>-->
	<span id="go_down"></span>

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>