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
			
			$susp_val_arr = array("NO","YES");
			$priv_val_arr = array("MEMBER","ADMIN","MODERATOR","FORCED");
			
			foreach($susp_val_arr as $susp_val){
				
				if(isset($_POST["col1value"]) && $_POST["col1value"] == $susp_val)
					$susp_val_opt .= '<option selected>'.$susp_val.'</option>';
				else
					$susp_val_opt .= '<option>'.$susp_val.'</option>';
				
			}
			
			foreach($priv_val_arr as $priv_val){
				
				if(isset($_POST["col2value"]) && $_POST["col2value"] == $priv_val)
					$priv_val_opt .= '<option selected>'.$priv_val.'</option>';
				else
					$priv_val_opt .= '<option>'.$priv_val.'</option>';
				
			}
						
			
			if($privilege != "ADMIN" && $privilege != "FORCED" )
					$no_view_privilege = "<span class='red'> ".$username." Sorry You do not have enough Privilege to view this Page</span>";
	
 if($privilege == "ADMIN" || $privilege == "FORCED"){ 
 
/////////////////////////////////////////IF MANAGE DB IS SET/////////////////////////////

			if(isset($_POST["manage_db"])){
				
				$cn1="";$cn2="";$cn3="";$cn4="";$cn5="";$cn6="";$cn7="";$cn8="";$cn9="";$cn10="";$cn11="";$cn12="";
				$all_cn="";$all_cv="";
				
////////////////////COL NAMES//////////////////////////////////////////////////////////
				$user = protect($_POST["user"]);		
				$tabname = protect($_POST["tabname"]);
				$col1name = protect($_POST["col1name"]);
				$col2name = protect($_POST["col2name"]);
				$col3name = protect($_POST["col3name"]);
				$col4name = protect($_POST["col4name"]);
				$col5name = protect($_POST["col5name"]);
				/*$col6name = protect($_POST["col6name"]);
				$col7name = protect($_POST["col7name"]);
				$col8name = protect($_POST["col8name"]);
				$col9name = protect($_POST["col9name"]);
				$col10name = protect($_POST["col10name"]);
				$col11name = protect($_POST["col11name"]);
				$col12name = protect($_POST["col12name"]);
				*/
				
////////////////////////COL VALUES/////////////////////////////////////////////////////////					
				$col1value = protect($_POST["col1value"]);
				$col2value = protect($_POST["col2value"]);				
				$col3value = protect($_POST["col3value"]);
				$col4value = protect($_POST["col4value"]);
				$col5value = protect($_POST["col5value"]);
				/*$col6value = protect($_POST["col6value"]);
				$col7value = protect($_POST["col7value"]);
				$col8value = protect($_POST["col8value"]);
				$col9value = protect($_POST["col9value"]);
				$col10value = protect($_POST["col10value"]);
				$col11value = protect($_POST["col11value"]);
				$col12value = protect($_POST["col12value"]);
				*/
				
								
				
			if(($col1value || $col2value || $col3value || $col4value || $col5value ) && $user ){
				
				if($tabname){
						
					if(verifyUser($user)){
					
						///////////SUSPENSION/////////////////
						
						if(isset($col1value)){
						
							
							$cn1 = "SUSPENSION_STATUS";
							$all_cn = "SUSPENSION_STATUS = ? ";
							
							//$col1value = preg_replace("#[^0-9\, (per month)]#isU", "", $col1value);
							
							$all_cv = $col1value;
							
								if(strtolower($tabname) == "update" && $col1value){
									
									/******IF A USER IS BEING LIFTED FROM SUSPENSION**************************/
									if($col1value == "NO"){
						
										/*********SET RECYCLING DEADLINE******************/
										$recyl_deadline = getRecyclingDeadline();
										
	//////////////////////////////////////////PDO QUERY////////////////////////////////////	
										$stmt2="";
										$sql = "UPDATE members SET CURRENT_PACKAGE='NONE', FLOW_DIRECTION='NONE', LOOP_STATUS='COMPLETE', TOTAL_DECL=0, TOTAL_PURGE=0, COMMENT1='', COMMENT2='', RECYCLING_DEADLINE=?, SUSPENSION_STATUS = ?   WHERE USERNAME = ? LIMIT 1";

										$stmt2 = $pdo_conn->prepare($sql);
										$stmt2->execute(array($recyl_deadline,$all_cv, $user));
	//////////////////////////////////////////PDO QUERY////////////////////////////////////	
										$stmt2="";
										$sql = "UPDATE declinations SET TOTAL=0 WHERE USERNAME = ? LIMIT 1";

										$stmt2 = $pdo_conn->prepare($sql);
										$stmt2->execute(array($user));
	//////////////////////////////////////////PDO QUERY////////////////////////////////////	
										$stmt2="";
										$sql = "UPDATE purges SET TOTAL=0 WHERE USERNAME = ? LIMIT 1";

										$stmt2 = $pdo_conn->prepare($sql);
										$stmt2->execute(array($user));
										
										$alert_user = '<span id="green" class="errors blink"><b>SUCCESS!!!</b></span>';
									}
									else{
	//////////////////////////////////////////PDO QUERY////////////////////////////////////	
										$stmt2="";
										$sql = "UPDATE members SET ".$all_cn."  WHERE USERNAME = ? LIMIT 1";

										$stmt2 = $pdo_conn->prepare($sql);
										
										
										if($stmt2->execute(array($all_cv, $user)))
											 $alert_user = '<span id="green" class="errors blink"><b>SUCCESS!!!</b></span>';
										else
											$alert_user = '<span id="red" class="errors blink "><b>FAILED!!!</b></span>';
									}
								
								
							}
							
						}
						
						///////////USER PRIVILEGE/////////////////
						
						if(isset($col2value) ){
							
							$cn2 = "USER_PRIVILEGE";
							$all_cn = "USER_PRIVILEGE = ?, COMMENT2 = ? ";													
							
							//$col2value = preg_replace("#[^0-9\% (discount)]#isU", "", $col2value);
							$all_cv = $col2value;
							
							if(strtolower($col2value) == "admin" || strtolower($col2value) == "moderator")
								$comment = '<b class="cyan">YOU HAVE BEEN PROMOTED TO '.$all_cv.'</b>';
							else
								$comment = '';
							
							if(strtolower($tabname) == "update" && $col2value){
									
//////////////////////////////////////////PDO QUERY////////////////////////////////////	
									$stmt2="";
									$sql = "UPDATE members SET ".$all_cn."  WHERE USERNAME = ? LIMIT 1";

									$stmt2 = $pdo_conn->prepare($sql);
									
									
									if($stmt2->execute(array($all_cv, $comment, $user)))
										 $alert_user = '<span id="green" class="errors blink"><b>SUCCESS!!!</b></span>';
									else
										$alert_user = '<span id="red" class="errors blink "><b>FAILED!!!</b></span>';
									
								}
							
						}
						
						///////////BANK NAME/////////////////
						
						if(isset($col3value) ){
							
							$cn3 = "BANK_NAME";
							$all_cn = "BANK_NAME = ? ";
							
							//$col3value = preg_replace("#[^0-9\% (discount)]#isU", "", $col3value);
							$all_cv = $col3value;
							
							if(strtolower($tabname) == "update" && $col3value){
									
//////////////////////////////////////////PDO QUERY////////////////////////////////////	
									$stmt2="";
									$sql = "UPDATE members SET ".$all_cn."  WHERE USERNAME = ? LIMIT 1";

									$stmt2 = $pdo_conn->prepare($sql);
									
									
									if($stmt2->execute(array($all_cv, $user)))
										 $alert_user = '<span id="green" class="errors blink"><b>SUCCESS!!!</b></span>';
									else
										$alert_user = '<span id="red" class="errors blink "><b>FAILED!!!</b></span>';
									
								}
							
						}
						
						///////////ACCOUNT NUMBER/////////////////
						
						if(isset($col4value) ){
							
							$cn4 = "ACCOUNT_NUMBER";
							$all_cn = "ACCOUNT_NUMBER = ? ";
							
							//$col4value = preg_replace("#[^0-9\% (discount)]#isU", "", $col4value);
							$all_cv = $col4value;
							
							if(strtolower($tabname) == "update" && $col4value){
									
//////////////////////////////////////////PDO QUERY////////////////////////////////////	
									$stmt2="";
									$sql = "UPDATE members SET ".$all_cn."  WHERE USERNAME = ? LIMIT 1";

									$stmt2 = $pdo_conn->prepare($sql);
									
									
									if($stmt2->execute(array($all_cv, $user)))
										 $alert_user = '<span id="green" class="errors blink"><b>SUCCESS!!!</b></span>';
									else
										$alert_user = '<span id="red" class="errors blink "><b>FAILED!!!</b></span>';
									
								}
							
						}
						
						///////////ACCOUNT NAME/////////////////
						
						if(isset($col5value) ){
							
							$cn2 = "ACCOUNT_NAME";
							$all_cn = "ACCOUNT_NAME = ? ";
							
							//$col5value = preg_replace("#[^0-9\% (discount)]#isU", "", $col5value);
							$all_cv = $col5value;
							
							if(strtolower($tabname) == "update" && $col5value){
									
//////////////////////////////////////////PDO QUERY////////////////////////////////////	
									$stmt2="";
									$sql = "UPDATE members SET ".$all_cn."  WHERE USERNAME = ? LIMIT 1";

									$stmt2 = $pdo_conn->prepare($sql);
									
									
									if($stmt2->execute(array($all_cv, $user)))
										 $alert_user = '<span id="green" class="errors blink"><b>SUCCESS!!!</b></span>';
									else
										$alert_user = '<span id="red" class="errors blink "><b>FAILED!!!</b></span>';
									
								}
							
						}
						
						////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
						echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert_user)."')</script>";
							
						
					}	
					else
					 $alert_user = '<span class="red">Sorry the target(<span class="blue">'.$user.'</span>) user was not found<br/> Please verify the username you entered and try again</span>';				
					
				}
				 else
					 $alert_user = '<span class="red">Sorry an unexpected error has occurred <br/> Sorry about that. </span>';
				
			}
			 else
				$alert_user = '<span class="red">Please Specified at least one Field Value and the target username* </span>';		
	
					
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
<title>DB MANAGER</title>
<?php require_once("include-html-headers.php")   ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>DB Manager</a> "  ;
			
	?>
	</header>



	<?php if($not_logged)   echo "<div class='view_user_wrapper'>".$not_logged."</div>";

	if(isset($no_view_privilege))   echo "<div class='view_user_wrapper'>".$no_view_privilege."</div>"; 

	?>

	<?php if($privilege == "ADMIN"  || $privilege == "FORCED"){  ?>
		
		
	<div class="view_user_wrapper" id="hide_vuwbb">
			<?php getMidPageScroll(); ?>

		<h1 class="h_bkg" id="f1">DB MANAGER</h1>  
		<div class="type_a">
			
			<?php if(isset($alert_user)) echo $alert_user; ?>
			
			<form method="post" action="db-manager">
				<fieldset>
					<label>Table Query:</label>
					<select name="tabname" class="only_form_textarea_inputs">
						<option <?php if(isset($tabname) && $tabname == "Update") echo 'selected' ?> >Update</option>
					</select>
					<label>Target Username:</label>			
					<input type="text" class="only_form_textarea_inputs" name="user" value="<?php if(isset($_POST["user"])) echo $_POST["user"]; ?>" />
					
					<label>Suspension Field:</label>
					<select name="col1name" class="only_form_textarea_inputs">				
							<option>SUSPENSION</option>
					</select>
					<label>Suspension Value*:</label>
					<input type="text" class="only_form_textarea_inputs" name="col1value" value="<?php if(isset($_POST["col1value"])) echo $_POST["col1value"]; ?>" />
					
					<label>Privilege Field:</label>
					<select name="col2name" class="only_form_textarea_inputs">
						<option>PRIVILEGE</option>				
					</select>
					<label>Privilege Value*:</label>
					<input type="text" class="only_form_textarea_inputs" name="col2value" value="<?php if(isset($_POST["col2value"])) echo $_POST["col2value"]; ?>" />
					<label>Bank Name Field:</label>
					<select name="col3name" class="only_form_textarea_inputs">
						<option>BANK NAME</option>				
					</select>
					<label>Bank Name Value*:</label>
					<input type="text" class="only_form_textarea_inputs" name="col3value" value="<?php if(isset($_POST["col3value"])) echo $_POST["col3value"]; ?>" />
					<label>Account Number Field:</label>
					<select name="col4name" class="only_form_textarea_inputs">
						<option>ACCOUNT NUMBER</option>				
					</select>
					<label>Account Number Value*:</label>
					<input type="text" class="only_form_textarea_inputs" name="col4value" value="<?php if(isset($_POST["col4value"])) echo $_POST["col4value"]; ?>" />
					<label>Account Holder Field:</label>
					<select name="col5name" class="only_form_textarea_inputs">
						<option>ACCOUNT HOLDER</option>				
					</select>
					<label>Account Holder Value*:</label>
					<input type="text" class="only_form_textarea_inputs" name="col5value" value="<?php if(isset($_POST["col5value"])) echo $_POST["col5value"]; ?>" />
				</fieldset>
				<input class="formButtons f_reset" type="reset" name="reset" value="RESET" />
				<input class="formButtons" type="submit" name="manage_db" value="PROCESS" />			
			</form>
				
		</div>	  
		
	</div>
	

	<?php } ?>

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>