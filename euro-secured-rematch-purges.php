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

$pack_opt="";$match_opt="";

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
				
	
	if($privilege != "ADMIN")
			$no_view_privilege = "<span class='red'> ".$username." Sorry You do not have enough Privilege to view this Page</span>";

	 if($privilege == "ADMIN"){ 
	 
								
			//////DEFINE ARRAY OF PACKAGES SO YOU CAN LOOP THROUGH ALL PACKAGES AND DO QUERY///////////////////////////////////
				
			$package_arr = getPackagesArray();
			
			foreach($package_arr as $pack){
				
				if(isset($_POST["pack"]) && ($_POST["pack"] == $pack))
					$pack_opt .= '<option selected>'.$pack.'</option>';
				else
					$pack_opt .= '<option>'.$pack.'</option>';
			}
			
			
			$mach_arr = array("MATCH-BY-TWO","MATCH-BY-ONE");
			foreach($mach_arr as $match){
				
				if(isset($_POST["match"]) && ($_POST["match"] == $match))
					$match_opt .= '<option selected>'.$match.'</option>';
				else
					$match_opt .= '<option>'.$match.'</option>';
			}
			


/*******************ON QUERY RUN****************************************/			
	 
			if(isset($_POST["run_query"])){
				
				$avn = protect($_POST["avn"]);
				$did = protect($_POST["did"]);
				$match = protect($_POST["match"]);
				$pack_name = protect($_POST["pack"]);
				
				if($did && $avn && $pack_name && $match){
				
					if(verifyAVN($username,$avn)){												
							
							$donation_table = 'euro_'.strtolower($pack_name).'_donations';
							$matching_table = 'euro_'.strtolower($pack_name).'_matching';
							
							
							///////IF THE PURGE WAS TRIGGERED FROM SECOND MATCH(MATCHED), 
							////THEN REMATCH RECEIVER BY ONE MATCH BY SETTING MATCH_STATUS TO SEMI-MATCHED ///////////////							
							
							if($match == "MATCH-BY-ONE"){
												
									$match_by = 'SEMI-MATCHED';
							///////////PDO QUERY////////////////////////////////////	
								
							$sql = "UPDATE ".$donation_table." SET  MATCH_STATUS = ?  WHERE ID = ?   LIMIT 1";

								$stmt = $pdo_conn_login->prepare($sql);
								if($stmt->execute(array($match_by, $did)))
									$alert = '<div id="green" class="errors blink">YOUR REMATCH  HAS BEEN SET SUCCESSFULLY!!!</div>';
								else
									$alert = '<div  class="errors blink">YOUR REQUEST TO SET REMATCH HAS FAILED!!!</div>';
									
								
							}///////IF THE PURGE WAS TRIGGERED FROM FIRST MATCH(SEMI-MATCHED), 
							////THEN REMATCH RECEIVER BY STANDARD TWO MATCH BY SETTING MATCH_STATUS TO AWAITING ///////////////							
							elseif($match == "MATCH-BY-TWO"){
								
								$match_by = 'AWAITING';
							///////////PDO QUERY////////////////////////////////////						
								$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = ?  WHERE ID = ?   LIMIT 1";
								$stmt = $pdo_conn_login->prepare($sql);
								if($stmt->execute(array($match_by, $did)))
									$alert = '<div id="green" class="errors blink">YOUR REMATCH  HAS BEEN SET SUCCESSFULLY!!!</div>';
								else
									$alert = '<div  class="errors blink">YOUR REQUEST TO SET REMATCH HAS FAILED!!!</div>';
									
							}
								
						
					}
					else{
							$alert = '<div class="errors blink">INCORRECT ACCOUNT VERIFICATION NUMBER(AVN),<br/> Please try again</div>';
					}
				}
				else{
					$alert = '<div class="errors blink">Please fill out all the fields</div>';
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
<title>SET PURGE REMATCH</title>
<?php require_once("include-html-headers.php")   ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>Purge Rematch</a> "  ;
			
	?>
	</header>



	<?php if(isset($not_logged))   echo "<div class='view_user_wrapper'>".$not_logged."</div>";

	if(isset($no_view_privilege))   echo "<div class='view_user_wrapper'>".$no_view_privilege."</div>"; 

	?>

	<?php if($privilege == "ADMIN"){  ?>
		
		
	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<?php echo getMidPageScroll(); ?>		

		<h1 class="h_bkg">SET PURGE REMATCH</h1>  
		
		<div class="type_a">
		
			<?php if(isset($alert)) echo $alert; ?>
					
			<input class="formButtons  start_btn" type="button" value="RUN" />
			<span></span>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">SET PURGE REMATCH<span class="close_modal">&times;</span></div>			
					<span class="red">ATTENTION: ADMIN, YOU ARE ABOUT TO SET A MEMBER FOR REMATCH, ARE YOU SURE YOU WANT TO EXECUTE THIS ACTION </span>
					<form method="post" action="euro-secured-rematch-purges">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="<?php if(isset($avn)) echo $avn;  ?>" class="only_form_textarea_inputs" />
						<label>DID<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter the target donation id" type="number" name="did" value="<?php if(isset($did)) echo $did;  ?>" class="only_form_textarea_inputs" />
						<label>MATCH TYPE<span class="red">*</span></label>
						<select required placeholder="Enter the match algo here" type="text" name="match" class="only_form_textarea_inputs" ><?php if(isset($match_opt)) echo $match_opt;  ?></select>						
						<label>PACKAGE<span class="red">*</span></label>
						<select required placeholder="Enter the target package" type="text" name="pack" class="only_form_textarea_inputs" ><?php if(isset($pack_opt)) echo $pack_opt; ?></select>
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