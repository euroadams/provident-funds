<?php      

session_start();
require_once ("phpfunctions.php");

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();

date_default_timezone_set("Africa/Lagos");

$not_logged="";$packages_option="";$action_option="";$auto_open_option="";$auto_unit_option="";
$acc_packages="";

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
			
			
			
/******************GET PACKAGES********************/	

////////////////////PDO QUERY////////////////////	
			
			$sql = "SELECT PACKAGE FROM packages ORDER  BY PACKAGE";

			$stmt1 = $pdo_conn->prepare($sql);
			$stmt1->execute();
			
			while($pack_row = $stmt1->fetch(PDO::FETCH_ASSOC)){
				
				$packages_option .= '<option>'.$pack_row["PACKAGE"].'</option>';
			}
			
			
			/********DEFINE/GET ACTION OPTIONS*********************************/
			
			$action_arr = array("SHOW","HIDE","UNLOCK","LOCK");
			
			foreach($action_arr as $action_val){
				
				if(isset($_POST["action"]) && $_POST["action"] == $action_val)
					$action_option .= '<option selected>'.$action_val.'</option>';
				else
					$action_option .= '<option>'.$action_val.'</option>';
				
			}
			
			
			
			/********DEFINE/GET AUTO OPEN OPTIONS*********************************/
			
			for($idx=1; $idx <= 100; $idx++){
				
				if(isset($_POST["auto_open"]) && $_POST["auto_open"] == $idx)
					$auto_open_option .= '<option selected>'.$idx.'</option>';
				else
					$auto_open_option .= '<option>'.$idx.'</option>';
					
				
			}
			
			/********DEFINE/GET AUTO OPEN UNIT OPTIONS*********************************/
			
			$auto_unit_arr = array("DAYS","HOURS");
			
			foreach($auto_unit_arr as $auto_unit_val){
				
				if(isset($_POST["auto_unit"]) && $_POST["auto_unit"] == $auto_unit_val)
					$auto_unit_option .= '<option selected>'.$auto_unit_val.'</option>';
				else
					$auto_unit_option .= '<option>'.$auto_unit_val.'</option>';
				
			}
			
						
			
			if($privilege != "ADMIN")
					$no_view_privilege = "<span class='red'> ".$username." Sorry You do not have enough Privilege to view this Page</span>";
	
 if($privilege == "ADMIN"){ 
 
/////////////////////////////////////////IF PROCESS_ ACTION IS SET/////////////////////////////

		if(isset($_POST["process_action"])){
																	
			/*******MAKE SURE A PACKAGE IS SELECTED***************/
			
			if(!empty($_POST["packages"])){
				
				$packages_arr = $_POST["packages"];
				$action = protect($_POST["action"]);
				if(isset($_POST["auto_open"]))
					$auto_open = protect($_POST["auto_open"]);
				if(isset($_POST["auto_unit"]))
					$auto_unit = protect($_POST["auto_unit"]);				
					
					
					/*******LOCK***************/ 					
					if(strtolower($action) == "lock"){
						
						/******CONVERT DAY TO SECOND 86400SEC IN 24HRS(1DAY)**********************/
						if(strtolower($auto_unit) == "days")
							$auto_open_time = (time() + (86400 * $auto_open));
						
						/******CONVERT HOUR TO SECOND 3600SEC IN 1HR**********************/
						elseif(strtolower($auto_unit) == "hours")
							$auto_open_time = (time() + (3600 * $auto_open));
							
						//////IF NO DURATION WAS PASSED THEN SET DEFAULT TO 24HRS///////////////////////////
							
						else
							$auto_open_time = (time() + 86400);
							
						
						foreach($packages_arr as $package){
														
								$package = protect($package);
														
						////////////////////PDO QUERY////////////////////	
						
							$sql = "UPDATE packages SET OPEN_TIME = ? WHERE PACKAGE = ?  LIMIT 1";

							$stmt2 = $pdo_conn->prepare($sql);
							if($stmt2->execute(array($auto_open_time, $package)))
								$acc_packages .= $package.'<br/>';
																
						}
						
						$alert_user = '<span class="red">Your have successfully <b>locked</b> the following packages:<br/>'.$acc_packages.'</span>';
						
					}
					
					
					/*******UNLOCK***************/ 					
					elseif(strtolower($action) == "unlock"){						
						
						foreach($packages_arr as $package){
											
							$package = protect($package);
							$auto_open_time = time();
							
							
						////////////////////PDO QUERY////////////////////	
						
							$sql = "UPDATE packages SET OPEN_TIME = ? WHERE PACKAGE = ?  LIMIT 1";

							$stmt2 = $pdo_conn->prepare($sql);
							if($stmt2->execute(array($auto_open_time, $package)))
								$acc_packages .= $package.'<br/>';
																
						}
						
						$alert_user = '<span class="red">Your have successfully <b>unlocked</b> the following packages:<br/>'.$acc_packages.'</span>';
						
					}
										
					
					
					/*******HIDE***************/ 					
					elseif(strtolower($action) == "hide"){						
						
						foreach($packages_arr as $package){
									
							$package = protect($package);									
							$vis_status = 'HIDDEN';
							
						////////////////////PDO QUERY////////////////////	
						
							$sql = "UPDATE packages SET VISIBILITY = ? WHERE PACKAGE = ?  LIMIT 1";

							$stmt2 = $pdo_conn->prepare($sql);
							if($stmt2->execute(array($vis_status, $package)))
								$acc_packages .= $package.'<br/>';
																
						}
						
						$alert_user = '<span class="red">Your have successfully <b>hidden</b> the following packages:<br/>'.$acc_packages.'</span>';
						
					}
										
					
					
					/*******SHOW***************/ 					
					elseif(strtolower($action) == "show"){						
						
						foreach($packages_arr as $package){
										
							$package = protect($package);										
							$vis_status = 'VISIBLE';
							
						////////////////////PDO QUERY////////////////////	
						
							$sql = "UPDATE packages SET VISIBILITY = ? WHERE PACKAGE = ?  LIMIT 1";

							$stmt2 = $pdo_conn->prepare($sql);
							if($stmt2->execute(array($vis_status, $package)))
								$acc_packages .= $package.'<br/>';
																
						}
						
						$alert_user = '<span class="red">Your have successfully made <b>visible</b> the following packages:<br/>'.$acc_packages.'</span>';
						
					}
				
				////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
					echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert_user)."')</script>";
								
								
					
			}
			 else
				$alert_user = '<span class="red">Please select the target package(s). </span>';
			
		}

	}				
	
}

else{

$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

}


?>





<!DOCTYPE HTML>
<html>
<head>
<title>PACKAGE LAUNCHER</title>
<?php require_once("include-html-headers.php")   ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>Package Launcher</a> "  ;
			
	?>
	</header>



	<?php if($not_logged)   echo "<div class='view_user_wrapper'>".$not_logged."</div>";

	if(isset($no_view_privilege))   echo "<div class='view_user_wrapper'>".$no_view_privilege."</div>"; 

	?>

	<?php if($privilege == "ADMIN" ){  ?>
		
		
	<div class="view_user_wrapper" id="hide_vuwbb">
			<?php getMidPageScroll(); ?>

		<h1 class="h_bkg" id="f1">PACKAGE LAUNCHER</h1>  
		<div class="type_a">
			
			<div class="errors"><?php if(isset($alert_user)) echo $alert_user; ?></div>
			
			<form method="post" action="package-launcher">
				<fieldset>
					<label>package*</label>
					<select multiple name="packages[]" class="only_form_textarea_inputs">
						<?php if($packages_option) echo $packages_option; ?>
					</select>
					
					<label>With Selected</label>
					<select name="action" class="only_form_textarea_inputs plock_select">				
						<?php if($action_option) echo $action_option; ?>
					</select>
					<div style="display:none;" class="plock_accomp">
						<label>Auto Open Time</label>
						<select name="auto_open" class="only_form_textarea_inputs">
							<?php if($auto_open_option) echo $auto_open_option; ?>
						</select>
		
						<label>Auto Open Time Unit</label>
						<select name="auto_unit" class="only_form_textarea_inputs">
							<?php if($auto_unit_option) echo $auto_unit_option; ?>			
						</select>	
					</div>
				</fieldset>
				<input class="formButtons" type="submit" name="process_action" value="PROCESS" />			
			</form>
				
		</div>	  
		
	</div>
	

	<?php } ?>

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>