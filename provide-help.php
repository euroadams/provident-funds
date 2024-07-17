<?php  
 
session_start();
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
$getdomain = getDomain();
$domain_name = getDomainName();

$curr_package="";$table="";
$std_dis="";$clsc_dis="";$prm_dis="";$elt_dis=""; $lrd_dis="";$mst_dis="";$roy_dis="";$ult_dis="";
$std_dis_css="";$clsc_dis_css="";$prm_dis_css="";$elt_dis_css="";
$lrd_dis_css="";$mst_dis_css="";$roy_dis_css="";$ult_dis_css="";

setPageTimeZone();


/***********SET LAUNCH/RESUME DATES******************************/

/*
$ref_time  = 1490135533 ;

$std_launch = ($ref_time  + (0));///0days from 21-03-2017////
$clsc_launch = ($ref_time  + (86400*0));///20days from 21-03-2017////
$prm_launch = ($ref_time  + (86400*0));///20days from 21-03-2017////
$elt_launch = ($ref_time  + (86400*0));///20days from 21-03-2017////

$lrd_launch = ($ref_time  + (86400*45));///20days from 21-03-2017////
$mst_launch = ($ref_time  + (86400*80));///20days from 21-03-2017////
$roy_launch = ($ref_time  + (86400*80));///40days from 21-03-2017////
$ult_launch = ($ref_time  + (86400*80));///40days from 21-03-2017////
*/

$std_launch = packageOpenTime("STANDARD");
$clsc_launch = packageOpenTime("CLASSIC");
$prm_launch = packageOpenTime("PREMIUM");
$elt_launch = packageOpenTime("ELITE");
$lrd_launch = packageOpenTime("LORD");
$mst_launch = packageOpenTime("MASTER");
$roy_launch = packageOpenTime("ROYAL");
$ult_launch = packageOpenTime("ULTIMATE");

////////////////////////////////////////////////////////////////

$username = $_SESSION["username"];

if(isset($_POST["start"])){
	
	if($username){
		
		$package = $_POST["pack"];
		$avn = $_POST["avn"];
		
		
		
		switch($package){
			
			case "STANDARD":{$amount_pledged = 5000; break;}
			case "CLASSIC":{$amount_pledged = 10000; break;}
			case "PREMIUM":{$amount_pledged = 20000; break;}
			case "ELITE":{$amount_pledged = 50000; break;}
			case "LORD":{$amount_pledged = 100000; break;}
			case "MASTER":{$amount_pledged = 200000; break;}
			case "ROYAL":{$amount_pledged = 500000; break;}
			case "ULTIMATE":{$amount_pledged = 1000000; break;}
			default:{$amount_pledged = 5000; break;}
		}
		
		$table = 'euro_'.strtolower($package).'_donations';
		
		$return_amt = ($amount_pledged * 2);
		
	/////////////////MAKE SURE THAT USERS DONT HAVE IMCOMPLETE CYCLES BEFORE THEY START A NEW ONE//////////////

	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT CURRENT_PACKAGE FROM members WHERE USERNAME = ? AND CURRENT_PACKAGE !='NONE'  LIMIT 1";

		$stmt1 = $pdo_conn_login->prepare($sql);
		$stmt1->execute(array($username));
		$chk_row = $stmt1->fetch(PDO::FETCH_ASSOC);
		$active_package = $stmt1->rowCount();
		if($active_package)
			$curr_package = $chk_row["CURRENT_PACKAGE"];
		
		if(!$active_package){
			
			/**********VERIFY AVN********************************************************/
			if(verifyAVN($username,$avn)){
				
				/*********CHECK IF LOH IS CLEARED**********/
				if(verifyLOH() || (getUserPrivilege($username) == "ADMIN" || getUserPrivilege($username) == "MODERATOR" )){
				
					///////////GENERATE TRANSACTION NUMBER/////////////////////////////////
								
					$trans_num = generateTransactionNumber($username);
							
					//////////////CHECK IF THIS IS THE FIRST DONATION OF THE PACKAGE TO KICK THE PLATFORM OFF////////////////////////////////////////////////////////////////////
					
					///////////PDO QUERY////////////////////////////////////	
				
					$sql = "SELECT ID FROM ".$table." LIMIT 1";

					$stmt2 = $pdo_conn_login->query($sql);

					if($stmt2->rowCount() && (getUserPrivilege($username) != "FORCED")){/////////IF NOT THE FIRST DONATION OF THE PACKAGE THEN///////////////////////////////////
									
						$time_of_pledge = time();
						$match_status = "AWAITING";
						
						//////////////START A PACKAGE AND TAKE A SLOT ON THE PACKAGE DONATION TAKE////////////////////////////////////////////////////////////////////
						
						///////////PDO QUERY////////////////////////////////////	
					
						$sql = "INSERT INTO ".$table." (USERNAME,PACKAGE,AMOUNT_PLEDGED,RETURN_AMOUNT,TIME_OF_PLEDGE,MATCH_STATUS,TRANS_NUMBER) VALUES(?,?,?,?,?,?,?)";

						$stmt3 = $pdo_conn_login->prepare($sql);

						if($stmt3->execute(array($username, $package, $amount_pledged, $return_amt, $time_of_pledge, $match_status, $trans_num))){
						
						/*****CATCH THE DONATION ID**********************/
						$did = $pdo_conn_login->lastInsertId();	
									
						//////////////////////////SINCE THE USER IS DONATING SET FLOW DIRECTION TO OUT//////////////////////////////////////////////////
						///////////PDO QUERY////////////////////////////////////	
							
							$sql = "UPDATE members SET CURRENT_PACKAGE = ?, RECYCLING_DEADLINE = '0', FLOW_DIRECTION = 'OUT' WHERE USERNAME = ? AND CURRENT_PACKAGE ='NONE'  LIMIT 1";

							$stmt4 = $pdo_conn_login->prepare($sql);

							if($stmt4->execute(array($package, $username))){
															
								//////////INSERT THE INFOS INTO TRANSACTION TABLE///////////////////////////////////////////////////////////
								
								$desc = 'PLEDGED DONATION';
								$trans_time = time();
								
								
								///////////PDO QUERY////////////////////////////////////	
								
								$sql = "INSERT INTO transactions (TRANS_NUMBER,USERNAME,DESCRIPTION,AMOUNT,TRANS_TIME,PACKAGE,DONATION_ID) VALUES(?,?,?,?,?,?,?)";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($trans_num,$username,$desc,$amount_pledged,$trans_time,$package,$did));
									
								//////////UPDATE PACKAGE FOLLOWERS/////////////////////////////////////////////////////	
									
									updatePackageFollowers($package);
															
								header("location:dash-board");
								exit();
								
							}
						}
						else{
							$alert = '<div class="errors blink">Sorry <span class="blue">'.$username.'</span>, Something went wrong please try again</div>';
						}
						
					}
					else{/////////IF  THIS IS THE FIRST DONATION OF THE PACKAGE OR PRIVILEGE IS FORCED, THEN ACTIVATE THE USER TO RECEIVE DONATION//////////////////////
								///////////////BY SETTING LOOP_STATUS TO SEMI-COMPLETE AND CONFIRMED TO YES////////////////////
									
						$time_of_pledge = time();
						$confirm_time = $time_of_pledge; //+ (60*30);//////SET CONFIRMATION TIME TO NOW INSTEAD OF SUGGESTED 30MINUTES AFTER PLEDGE///////////////////
						$match_status = "AWAITING";
						$loop_status = "SEMI-COMPLETE";
						$confirm_status = "YES";
						$paid_or_decl = "PAID";
						
						//////////////START A PACKAGE AND TAKE A SLOT ON THE PACKAGE DONATION TABLE////////////////////////////////////////////////////////////////////
						
						///////////PDO QUERY////////////////////////////////////	
					
						$sql = "INSERT INTO ".$table." (USERNAME,PACKAGE,AMOUNT_PLEDGED,RETURN_AMOUNT,TIME_OF_PLEDGE,MATCH_STATUS,CONFIRMED,CONFIRM_TIME,LOOP_STATUS,PAID_OR_DECLINED, TRANS_NUMBER) VALUES(?,?,?,?,?,?,?,?,?,?,?)";

						$stmt3 = $pdo_conn_login->prepare($sql);

						if($stmt3->execute(array($username, $package, $amount_pledged, $return_amt, $time_of_pledge, $match_status, $confirm_status, $confirm_time, $loop_status, $paid_or_decl, $trans_num))){
									
						/*****CATCH THE DONATION ID**********************/
						$did = $pdo_conn_login->lastInsertId();	
						
						//////////////////////////SINCE THE USER IS DONATING TO KICK OFF THE PACKAGE SET FLOW DIRECTION TO IN//////////////////////////////////////////////////
						///////////PDO QUERY////////////////////////////////////	
							
							$sql = "UPDATE members SET CURRENT_PACKAGE = ?, RECYCLING_DEADLINE = '0', FLOW_DIRECTION = 'IN', LOOP_STATUS = 'SEMI-COMPLETE' WHERE USERNAME = ? AND CURRENT_PACKAGE ='NONE'  LIMIT 1";

							$stmt4 = $pdo_conn_login->prepare($sql);

							if($stmt4->execute(array($package, $username))){																									
								
								//////////INSERT THE INFOS INTO TRANSACTION TABLE///////////////////////////////////////////////////////////
								
								$desc = 'MADE DONATION';
								$trans_time = time();
								
								
								///////////PDO QUERY////////////////////////////////////	
								
								$sql = "INSERT INTO transactions (TRANS_NUMBER,USERNAME,DESCRIPTION,AMOUNT,TRANS_TIME,PACKAGE,DONATION_ID) VALUES(?,?,?,?,?,?,?)";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($trans_num,$username,$desc,$amount_pledged,$trans_time,$package,$did));
								
								
								//////////UPDATE PACKAGE FOLLOWERS/////////////////////////////////////////////////////	
									updatePackageFollowers($package);
								
								header("location:dash-board");
								exit();
								
							}
						}
						else{
							$alert = '<div class="errors blink">Sorry <span class="blue">'.$username.'</span>, Something went wrong please try again</div>';
						}
						
					}	
				
				}
				else{
					$alert = '
								<div class="modal" style="display:block;">																						
									<div class="modal_content">
										<div class="modal_header clear">TESTIMONIAL GATE CHECK<span class="close_modal">&times;</span></div>			
										<div class="errors">
											UNFORTUNATELY, YOU CANNOT INITIATE A NEW TRANSACTION UNTIL YOU SUBMIT A TESTIMONIAL OF THE HELP YOU GOT
											IN YOUR PREVIOUS TRANSACTION.<br/> <a class="links" href="loh">Write Your Testimonial Now</a>										
										</div>
									</div>
								</div>			
							';
				}
			
			}
			else{
				$alert = '<div class="errors blink">SORRY, AVN VERIFICATION FAILED!!!</div>';
			}
		
		}
		else{
			$alert = '<div class="errors blink">Sorry <span class="blue">'.$username.'</span>, you have to wait till your current '.$curr_package.' package cycle is complete before you can start a new one.</div>';
		}
		
		
		
	
	
	}else{
		header("location:login");
		exit();
	}
	
	
}

?>

<!DOCTYPE HTML>
<html>
<head>

<title>PROVIDE HELP</title>
<?php require_once('include-html-headers.php')   ?>

<?php
	
	$user_priv = getUserPrivilege($username);
	
	if($std_launch > time()){
				
		echo '<script>startStandardCountDown('.$std_launch.')</script>';
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
				
			$std_dis = 'disabled';
			$std_dis_css = 'disabled';
		}
	}
	if($clsc_launch  > time()){
				
		echo '<script>startClassicCountDown('.$clsc_launch.')</script>';
		
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
				
			$clsc_dis = 'disabled';
			$clsc_dis_css = 'disabled';
		}
	}
	if($prm_launch  > time()){
				
		echo '<script>startPremiumCountDown('.$prm_launch.')</script>';
		
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
					
			$prm_dis = 'disabled';
			$prm_dis_css = 'disabled';
		}
	}
	if($elt_launch  > time()){
				
		echo '<script>startEliteCountDown('.$elt_launch.')</script>';
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
			
			$elt_dis = 'disabled';
			$elt_dis_css = 'disabled';
		}
			
	}
	if($lrd_launch > time()){
				
		echo '<script>startLordCountDown('.$lrd_launch.')</script>';
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
			
			$lrd_dis = 'disabled';
			$lrd_dis_css = 'disabled';
		}
	}
	if($mst_launch  > time()){
				
		echo '<script>startMasterCountDown('.$mst_launch.')</script>';
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
			$mst_dis = 'disabled';
			$mst_dis_css = 'disabled';
		}
	}
	if($roy_launch  > time()){
				
		echo '<script>startRoyalCountDown('.$roy_launch.')</script>';
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
			$roy_dis = 'disabled';
			$roy_dis_css = 'disabled';
		}
	}
	if($ult_launch  > time()){
				
		echo '<script>startUltimateCountDown('.$ult_launch.')</script>';
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
			$ult_dis = 'disabled';
			$ult_dis_css = 'disabled';
		}
	}


?>
<style>

</style>


</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')     ?>

	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		echo "<a href='provide-help' title=>Provide Help</a> "  ;
				
		?>
	</header>	

	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<?php echo getMidPageScroll(); ?>	
	
		<?php if(isset($alert)) echo $alert;?>
		
		
		<h1 class="h_bkg"><img class="min_img" src="wealth-island-images/icons/strelka_dwn.png" /> PLEASE SELECT A PACKAGE <img class="min_img" src="wealth-island-images/icons/strelka_dwn.png" /></h1>
		<span class="red">Please only select a package that you are comfortable with and that corresponds to the cash you have at hand</span><br/><br/>						
		<?php if(packageVisibility("STANDARD")){?>
		<div class="packages std_pack">
			<h1>STANDARD <?php echo getPackageFollowers("STANDARD"); ?></h1>
			<h2>Donate<br/> ₦5,000</h2><hr/>
			<?php echo getPackFeats(); ?>
			<h2>Get<br/> ₦10,000</h2>	
			<input type="button" class="formButtons start_btn <?php echo $std_dis_css ?>"  id="std_btn" <?php echo $std_dis ?> value="START" />
			<div class="std_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">JOIN STANDARD PACKAGE<span class="close_modal">&times;</span></div>						
					<form action="provide-help" method="post">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<input type="hidden" name="pack" value="STANDARD"  />
						<input type="submit" name="start" class="formButtons" id="std_btn" value="JOIN" />
					</form>
				</div>
			</div>			
			
		</div>
		<?php } ?>
		<?php if(packageVisibility("CLASSIC")){?>		
		<div class="packages clsc_pack">
			<h1>CLASSIC <?php echo getPackageFollowers("CLASSIC"); ?></h1>
			<h2>Donate<br/> ₦10,000</h2><hr/>
			<?php echo getPackFeats(); ?>
			<h2>Get<br/> ₦20,000</h2>
			<input type="button" class="formButtons start_btn <?php echo $clsc_dis_css ?>" id="clsc_btn" <?php echo $clsc_dis ?> value="START" />
			<div class="clsc_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">JOIN CLASSIC PACKAGE<span class="close_modal">&times;</span></div>			
					<form action="provide-help" method="post">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<input type="hidden" name="pack" value="CLASSIC"  />
						<input type="submit" name="start" class="formButtons" id="clsc_btn" value="JOIN" />
					</form>
				</div>
			</div>			
		</div>
		<?php } ?>
		<?php if(packageVisibility("PREMIUM")){?>
		<div class="packages prm_pack">
			<h1>PREMIUM <?php echo getPackageFollowers("PREMIUM"); ?></h1>
			<h2>Donate<br/> ₦20,000</h2><hr/>
			<?php echo getPackFeats(); ?>
			<h2>Get<br/> ₦40,000</h2>			
			<input type="button" class="formButtons start_btn <?php echo $prm_dis_css ?>"  id="prm_btn" <?php echo $prm_dis ?> value="START" />
			<div class="prm_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">JOIN PREMIUM PACKAGE<span class="close_modal">&times;</span></div>			
					<form action="provide-help" method="post">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<input type="hidden" name="pack" value="PREMIUM"  />
						<input type="submit" name="start" class="formButtons" id="prm_btn" value="JOIN" />
					</form>
				</div>
			</div>			
		</div>
		<?php } ?>
		<?php if(packageVisibility("ELITE")){?>
		<div class="packages elt_pack">
			<h1>ELITE <?php echo getPackageFollowers("ELITE"); ?></h1>
			<h2>Donate<br/> ₦50,000</h2><hr/>
			<?php echo getPackFeats(); ?>
			<h2>Get<br/> ₦100,000</h2>			
			<input type="button" class="formButtons start_btn <?php echo $elt_dis_css ?>"  id="elt_btn" <?php echo $elt_dis ?> value="START" />
			<div class="elt_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">JOIN ELITE PACKAGE<span class="close_modal">&times;</span></div>			
					<form action="provide-help" method="post">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<input type="hidden" name="pack" value="ELITE"  />
						<input type="submit" name="start" class="formButtons" id="elt_btn" value="JOIN" />
					</form>
				</div>
			</div>			
		</div>					
		<!--<br/><br/><h1 class="h_bkg"><img class="min_img" src="wealth-island-images/icons/strelka_up.png" /> UPCOMING PACKAGES <img class="min_img" src="wealth-island-images/icons/strelka_up.png" /></h1>-->
		<?php } ?>
		<?php if(packageVisibility("LORD")){?>
		<div class="packages lrd_pack">
			<h1>LORD <?php echo getPackageFollowers("LORD"); ?></h1>
			<h2>Donate<br/> ₦100,000</h2><hr/>
			<?php echo getPackFeats(); ?>
			<h2>Get<br/> ₦200,000</h2>			
			<input type="button" class="formButtons start_btn <?php echo $lrd_dis_css ?>" id="lrd_btn" <?php echo $lrd_dis ?> value="START" />
			<div class="lrd_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">JOIN LORD PACKAGE<span class="close_modal">&times;</span></div>			
					<form action="provide-help" method="post">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<input type="hidden" name="pack" value="LORD"  />
						<input type="submit" name="start" class="formButtons" id="lrd_btn" value="JOIN" />
					</form>
				</div>
			</div>			
		</div>
		<?php } ?>
		<?php if(packageVisibility("MASTER")){?>
		<div class="packages mst_pack">
			<h1>MASTER <?php echo getPackageFollowers("MASTER"); ?></h1>
			<h2>Donate<br/> ₦200,000</h2><hr/>
			<?php echo getPackFeats(); ?>
			<h2>Get<br/> ₦400,000</h2>		
			<input type="button" class="formButtons start_btn <?php echo $mst_dis_css ?>" id="mst_btn"  <?php echo $mst_dis ?> value="START" />
			<div class="mst_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">JOIN MASTER PACKAGE<span class="close_modal">&times;</span></div>
					<form action="provide-help" method="post">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<input type="hidden" name="pack" value="MASTER"  />
						<input type="submit" name="start" class="formButtons" id="mst_btn" value="JOIN" />
					</form>
				</div>
			</div>			
		</div>
		<?php } ?>
		<?php if(packageVisibility("ROYAL")){?>
		<div class="packages roy_pack">
			<h1>ROYAL <?php echo getPackageFollowers("ROYAL"); ?></h1>
			<h2>Donate<br/> ₦500,000</h2><hr/>
			<?php echo getPackFeats(); ?>
			<h2>Get<br/> ₦1,000,000</h2>	
			<input type="button" class="formButtons start_btn <?php echo $roy_dis_css ?>" id="roy_btn"  <?php echo $roy_dis ?> value="START" />
			<div class="roy_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">JOIN ROYAL PACKAGE<span class="close_modal">&times;</span></div>			
					<form action="provide-help" method="post">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<input type="hidden" name="pack" value="ROYAL"  />
						<input type="submit" name="start" class="formButtons" id="roy_btn" value="JOIN" />
					</form>
				</div>
			</div>			
		</div>
		<?php } ?>
		<?php if(packageVisibility("ULTIMATE")){?>
		<div class="packages ult_pack">
			<h1>ULTIMATE <?php echo getPackageFollowers("ULTIMATE"); ?></h1>
			<h2>Donate<br/> ₦1,000,000</h2><hr/>
			<?php echo getPackFeats(); ?>
			<h2>Get<br/> ₦2,000,000</h2>
			<input type="button" class="formButtons start_btn <?php echo $ult_dis_css ?>" <?php echo $ult_dis ?> id="ult_btn" value="START" />
			<div class="ult_timer_wrapper"></div>
			<div class="modal">																						
				<div class="modal_content">
					<div class="modal_header clear">JOIN ULTIMATE PACKAGE<span class="close_modal">&times;</span></div>			
					<form action="provide-help" method="post">
						<label>AVN<span class="red">*</span></label>
						<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
						<input type="hidden" name="pack" value="ULTIMATE"  />
						<input type="submit" name="start" class="formButtons" id="ult_btn" value="JOIN" />
					</form>
				</div>
			</div>			
		</div>
		<?php } ?>		
				
	</div>
	<?php require_once('eurofooter.php')     ?>
</div>
</body>
</html>