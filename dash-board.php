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

$active_package="";$inc_amt="";$pack_css="";$trx_num="";$pack_btn_css="";$flow_dir="";$alert="";$comments="";
 $std_dis="";$clsc_dis="";$prm_dis="";$elt_dis="";$lrd_dis="";$mst_dis="";$roy_dis="";$ult_dis="";
 $std_dis_css="";$clsc_dis_css="";$prm_dis_css="";$elt_dis_css="";$lrd_dis_css="";$mst_dis_css="";$roy_dis_css="";$ult_dis_css="";

setPageTimeZone();


////////MONITOR UPLOAD MAX POST LENGTH//////////////////////////////////////////
$granted = checkUploadLength();

if($granted == "ERROR")
	$alert2 = "<span class='red'><b>Sorry the file you are trying to upload is too large (maximum allowed file size is 5MB).</b></span>" ;


$username = $_SESSION["username"];

if($username){

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

	
	$page_self = getReferringPage("qstr url");
	//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////
	
	if(isset($_COOKIE["form_gate_response"])){
		
		$alert2 = $_COOKIE["form_gate_response"];
		
			
	/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

			setcookie("form_gate_response", "", (time() -  1800));

	}

	
		
/*******************IF AJAX POST HERE FOR CLEARING OF COMMENTS**************************************************************/		
		
		if(isset($_POST["clear_comm"])){
							
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "UPDATE members SET COMMENT1 = '', COMMENT2 = '' WHERE USERNAME = ?  LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);
			$stmt->execute(array($username));
		}		
		
	
/*******************IF AJAX POST HERE FOR HIDING OT AVN**************************************************************/		
		
		if(isset($_POST["hide_avn"])){
							
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "UPDATE members SET OT_AVN = '' WHERE USERNAME = ?  LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);
			$stmt->execute(array($username));
		}		
		
	
	
/****************CHECK IF THE USER HAS ACTIVE OR INCOMPLETE PACKAGE LOOP AWAITING****************************************************/
	
	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT CURRENT_PACKAGE, FLOW_DIRECTION FROM members WHERE USERNAME = ? AND CURRENT_PACKAGE !='NONE'  LIMIT 1";

		$stmt1 = $pdo_conn_login->prepare($sql);
		$stmt1->execute(array($username));
		$chk_row = $stmt1->fetch(PDO::FETCH_ASSOC);
		$active_package = $stmt1->rowCount();
		
		if($active_package ||  isset($_POST["fcfm"])){/////////IF THERE IS AN ACTIVE PACKAGE THEN SHOW THE USER MATCH RELATED INFOS////////////////////////////////////
				
				$curr_package = $chk_row["CURRENT_PACKAGE"];
				$flow_dir = $chk_row["FLOW_DIRECTION"];
				$donation_table = 'euro_'.strtolower($curr_package).'_donations';
				$matching_table = 'euro_'.strtolower($curr_package).'_matching';
				
				
				switch($curr_package){///////DETERMINE THE INCOMING DONATION AMOUNT AND CSS CLASS FOR THE PACKAGE//////////////////////////////////////////////////////
					
					case "STANDARD":{$inc_amt = 5000; $pack_css = 'std_pack'; $pack_btn_css = 'std_btn'; break;}
					case "CLASSIC":{$inc_amt = 10000; $pack_css = 'clsc_pack'; $pack_btn_css = 'clsc_btn'; break;}
					case "PREMIUM":{$inc_amt = 20000; $pack_css = 'prm_pack'; $pack_btn_css = 'prm_btn'; break;}
					case "ELITE":{$inc_amt = 50000; $pack_css = 'elt_pack'; $pack_btn_css = 'elt_btn'; break;}
					case "LORD":{$inc_amt = 100000; $pack_css = 'lrd_pack'; $pack_btn_css = 'lrd_btn'; break;}
					case "MASTER":{$inc_amt = 200000; $pack_css = 'mst_pack'; $pack_btn_css = 'mst_btn'; break;}
					case "ROYAL":{$inc_amt = 500000; $pack_css = 'roy_pack'; $pack_btn_css = 'roy_btn'; break;}
					case "ULTIMATE":{$inc_amt = 1000000; $pack_css = 'ult_pack'; $pack_btn_css = 'ult_btn'; break;}
					
				}
				
				/**********************WHEN A PAYER DECLINE PAYMENT BY I CANNOT PAY OR WHEN A PAYER IS PURGED******************************************************************/
				
				
				if(isset($_POST["decline_pay"]) || isset($_POST["purge"])){
					
					$payer_did = protect($_POST["payer_did"]);
					
					if(isset($_POST["decline_pay"]))
						$type = "DECLINATION";
					
					elseif(isset($_POST["purge"]))
						$type = "PURGING";
					
					handleDeclination($payer_did,$donation_table,$matching_table,$type);
					
					////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
					echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=')</script>";
						
				}
				
				
				/****************WHEN A PAYER SUBMITS PROOF OF PAYMENT AFTER PAYING************************************************************************/
				
				if(isset($_POST["have_paid"])){
										
					$pay_method = $_POST["pay_method"];
					$slip_name = $_POST["slip_name"];
					$payer_did = $_POST["payer_did"];
					
						
						/////////////////UPLOADING PROOF///////////////////////////////////////////////////////////////////////////////////////////////

						if($_FILES['proof']['name']){

							$filepath = "wealth-island-uploads/proof_of_payments/".basename($_FILES['proof']['name']);
							
							//////MAKE THE UPLOAD FOLDER INVISIBLE TO USERS

							$proof = basename($_FILES['proof']['name']);

							$uok = 1;

							$file = $_FILES['proof']['name'];

							$filetypeext = pathinfo($filepath,PATHINFO_EXTENSION);

							if((strtolower($filetypeext) !="png") && (strtolower($filetypeext) !="jpg" ) && (strtolower($filetypeext) !="jpeg" ) && ($filepath!="wealth-island-uploads/proof_of_payments/")){

								$uok=0;
								$err1 = true;

							}

							/////////////RENAME THE FILE IF THE FILE NAME ALREADY EXISTS//////////////////////////////////////////////

							if($file && file_exists($filepath)){

								$i = 0;
								while(file_exists($filepath)){
									
									
									$newfn = explode(".", $file);
									
									
									$newfn = $newfn[0]."(".$i.")".".".$newfn[1];
									
									$i++;
									
									$filepath = "wealth-island-uploads/proof_of_payments/".$newfn;
									
									
								}

								$filepath = "wealth-island-uploads/proof_of_payments/".$newfn;

								$proof = $newfn;

							}

							else
								$proof = $_FILES["proof"]["name"];

							if($_FILES['proof']['size'] > 5242880){ ////////RESTRICT SIZE TO 5MB////////////

								$uok=0;
								$err2 = true;

								
							}

							if($uok == 0){
								
								if(isset($err1))
									$alert2 .= '<div class="errors blink">ERROR: THERE WAS AN ERROR UPLOADING YOUR PROOF OF PAYMENT<br/> PLEASE TRY AGAIN.<br/>NOTE ONLY JPEG/PNG IS ALLOWED</div>';
								if(isset($err2))	
									$alert2 .= '<div class="errors blink">ERROR: THERE WAS AN ERROR UPLOADING YOUR PROOF OF PAYMENT<br/> PLEASE TRY AGAIN.<br/>THE FILE WAS TOO LARGE, MAXIMUM SIZE ALLOWED IS 5MB.</div>';
								
							}
							elseif ($uok){
								if(move_uploaded_file($_FILES['proof']['tmp_name'],$filepath)){
									
									$time_of_pay = time();
									
									///////////NOW UPDATE THE POP IN MATCHING TABLE  AND SET PAID_OR_DECLINED TO PAID AND STOP HIS TIMER BY SETTING PAYER_DEADLINE TO ZERO////////////////////////////////////////////////////////////				
									///////////PDO QUERY////////////////////////////////////	
									
									$sql = "UPDATE ".$matching_table." SET  METHOD_OF_PAY = ?, PAYMENT_SLIP_NAME = ?, UPLOADED_PROOF = ? , PAID_OR_DECLINED = 'PAID', TIME_OF_PAY = ?, PAYER_DEADLINE = '0' WHERE PAYER_DID = ?   LIMIT 1";

									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($pay_method,$slip_name,$proof,$time_of_pay,$payer_did));
									
									///////////NOW ALSO  UPDATE HIS PAID_OR_DECLINED TO PAID IN DONATION TABLE////////////////////////////////////////////////////////////				
									///////////PDO QUERY////////////////////////////////////	
									
									$sql = "UPDATE ".$donation_table." SET  PAID_OR_DECLINED = 'PAID' WHERE ID = ?   LIMIT 1";

									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($payer_did));
									
									///////////NOW RECORD THE PAYER'S TRANSACTION IN THE TRANSACTION TABLE////////////////////////////////////////////////////////////				
									
									////////////FIRST GET THE PAYER USERNAME AND AMOUNT PLEDGED FROM DONATION TABLE////////////////////////////////////////////////
									///////////PDO QUERY////////////////////////////////////	
					
									$sql = "SELECT USERNAME,AMOUNT_PLEDGED FROM ".$donation_table." WHERE ID = ?  LIMIT 1";
									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($payer_did));
									$payer_row = $stmt->fetch(PDO::FETCH_ASSOC);
									
									$payer = $payer_row["USERNAME"];
									$amt_pledged = $payer_row["AMOUNT_PLEDGED"];
									
									$desc = 'MADE DONATION';
									$trans_time = time();
									
									//////////INSERT THE INFOS INTO TRANSACTION TABLE///////////////////////////////////////////////////////////
									
									///////////PDO QUERY////////////////////////////////////	
									
									$sql = "UPDATE transactions SET DESCRIPTION=?, TRANS_TIME=? WHERE DONATION_ID=?";

									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($desc,$trans_time,$payer_did));

									$alert2 = '<div id="green" class="errors blink"> YOUR PROOF OF PAYMENT HAS BEEN SUBMITTED SUCCESSFULLY.</div>';
								}

							}
							else if($_FILES["proof"]["name"]){
							$alert2 = '<div class="errors blink">ERROR: THERE WAS AN ERROR UPLOADING YOUR PROOF OF PAYMENT<br/> PLEASE TRY AGAIN.<br/>NOTE ONLY JPEG/PNG IS ALLOWED AND MAXIMUM ALLOWED SIZE IS 5MB.</div>';

							}

						}
					
					////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
					echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert2)."')</script>";
					
				}
				
				/****************WHEN A USER CONFIRMS PAYMENT************************************************************************/
				if(isset($_POST["final_confirm"])){
					
					$avn = $_POST["avn"];
					$rec_did = $_POST["rec_did"];
					$payer_did = $_POST["payer_did"];
					
					if(isset($_POST["dtab"]))
						$donation_table = protect($_POST["dtab"]);
					if(isset($_POST["mtab"]))
						$matching_table = protect($_POST["mtab"]);
					
										
					/*******************************************************************************
					FIRST GET THE RECEIVER USERNAME AND AMOUNT PLEDGED FROM DONATION TABLE
					ALSO CHECK AGAINST POSSIBLE SECURITY THREATS BY MAKING SURE ONLY THE USER CONCERNED
					AND ADMINS CAN CONFIRM PAYMENTS
					*********************************************************************************/
				///////////PDO QUERY////////////////////////////////////	
	
					$sql = "SELECT USERNAME,AMOUNT_PLEDGED FROM ".$donation_table." WHERE ID = ?  LIMIT 1";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array($rec_did));
					$rec_row = $stmt->fetch(PDO::FETCH_ASSOC);
					
					$receiver = $rec_row["USERNAME"];
					$amt_pledged = $rec_row["AMOUNT_PLEDGED"];
					if(getUserPrivilege($username) == "ADMIN")
						$user = $username;
					else
						$user = $receiver;
										
					if(verifyAVN($user,$avn)){
						
					/***************CONFIRM THE PAYER AND COMPLETE YOUR LOOP********************************************************************/
						
					//////////////FIRST FETCH THE PAYER DETAILS FROM THE MATCHING TABLE WHERE PAYER MADE PAYMENT////////////////////////////////////////////////////////
					
					///////////PDO QUERY////////////////////////////////////	
						
						$sql = "SELECT PAYER_DID,PAYER_USERNAME FROM ".$matching_table." WHERE REC_DID = ? AND PAYER_DID = ?  AND CONFIRMED != 'YES' AND PAID_OR_DECLINED != 'DECLINED'   LIMIT 1";

						$stmt = $pdo_conn_login->prepare($sql);
						$stmt->execute(array($rec_did,$payer_did));
						$row = $stmt->fetch(PDO::FETCH_ASSOC);					
						$payer_did = $row["PAYER_DID"];
						$payer = $row["PAYER_USERNAME"];
						$confirm_time = time();
						
						if($stmt->rowCount()){
				
							/*******PAYER HANDLE***********/
							////////////////NOW CONFIRM YOUR PAYER IN DONATION TABLE AND SET HIS MATCH_STATUS TO AWAITING SO HE CAN BE MATCHED TO RECEIVE ////////////////////
							///////////PDO QUERY////////////////////////////////////	
							
							$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = 'AWAITING',  LOOP_STATUS = 'SEMI-COMPLETE', CONFIRMED = 'YES', CONFIRM_TIME = ?  WHERE ID = ?   LIMIT 1";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($confirm_time,$payer_did));///////////PDO QUERY////////////////////////////////////	
							
							////////////////ALSO CONFIRM YOUR PAYER IN MATCHING TABLE ////////////////////
							///////////PDO QUERY////////////////////////////////////	
							$sql = "UPDATE ".$matching_table." SET  CONFIRMED = 'YES', CONFIRM_TIME = ?  WHERE PAYER_DID = ?   LIMIT 1";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($confirm_time,$payer_did));
						
							////////////////ALSO UPDATE THE PAYER'S LOOP_STATUS TO SEMI-COMPLETE AND FLOW_DIRECTION TO IN IN MEMBERS TABLE ////////////////////
							$cfrm_commt = '<span id="green" >YOU HAVE BEEN CONFIRMED AND AWAITING MATCH TO RECEIVE</span>';
							///////////PDO QUERY////////////////////////////////////	
							$sql = "UPDATE members SET  LOOP_STATUS = 'SEMI-COMPLETE', FLOW_DIRECTION = 'IN', COMMENT1 = ? WHERE USERNAME = ?   LIMIT 1";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($cfrm_commt, $payer));
							
							////////NOW CHECK IF THE USER BEING CONFIRMED WAS REFERRED AND ALSO CONFIRM THE REFERRAL INCENTIVE////////////
							///////////PDO QUERY////////////////////////////////////	
							$sql = "SELECT * FROM referrals WHERE REFERRED = ? AND CONFIRMATION = 'PENDING' LIMIT 1";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($payer));
							if($stmt->rowCount()){
								
								///////////PDO QUERY////////////////////////////////////	
								$sql = "UPDATE referrals SET CONFIRMATION = 'CONFIRMED' WHERE REFERRED = ? AND CONFIRMATION = 'PENDING' LIMIT 1";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($payer));
								
								
							}
							
							
							//////////INSERT THE INFOS INTO TRANSACTION TABLE FOR PAYER///////////////////////////////////////////////////////////
							
							/****PAYER*********/
							///////////PDO QUERY////////////////////////////////////	
					
							$sql = "UPDATE transactions SET STATUS='PAID' WHERE DONATION_ID=? ";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($payer_did));
							
							
							
							
							/*******RECEIVER HANDLE***********/
							////////////////UPDATE THE RECEIVER COUNTER IN DONATION TABLE ////////////////////
							///////////PDO QUERY////////////////////////////////////	
							$sql = "UPDATE ".$donation_table." SET REC_COUNTER = (REC_COUNTER + 1)  WHERE ID = ?   LIMIT 1";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($rec_did));
							
				
					
							////////////////CHECK IF THE TWO ASSIGNED MEMBERS HAS PAID COMPLETELY THEN COMPLETE YOUR LOOP CYCLE ////////////////////
																					
							///////////PDO QUERY////////////////////////////////////	
							$sql = "SELECT REC_COUNTER FROM ".$donation_table."  WHERE ID = ? AND REC_COUNTER = 2   LIMIT 1";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($rec_did));
							
							///////IF RECEIVING SECOND DONATION/////////////////////////////////////////////////					
							if($stmt->rowCount()){
							
								////////////////FINALLY  COMPLETE YOUR LOOP CYCLE IN DONATION TABLE ////////////////////
								///////////PDO QUERY////////////////////////////////////	
								$sql = "UPDATE ".$donation_table." SET  LOOP_STATUS = 'COMPLETE', MATCH_STATUS = 'MATCHED-COMPLETE'  WHERE ID = ?   LIMIT 1";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($rec_did));
																												
								///////////NOW RECORD THE SECOND DONATION TRANSACTION IN THE TRANSACTION TABLE FOR THE RECEIVER////////////////////////////////////////////////////////////				
								
								$desc = 'RECEIVED';
								$trans_time = time();
								
								/****RECEIVER*********/
								///////////PDO QUERY////////////////////////////////////	
						
								$sql = "UPDATE transactions SET DONATION2=?, DONATION2_TIME=?, STATUS = 'SUCCESSFUL' WHERE DONATION_ID=? ";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($desc,$trans_time,$rec_did));
								
								/*********SET RECYCLING DEADLINE******************/
								$recyl_deadline = getRecyclingDeadline();
								
								////////////////ALSO COMPLETE YOUR LOOP CYCLE AND SET YOUR NEW RECYCLING DEADLINE IN MEMBERS TABLE ////////////////////
								///////////PDO QUERY////////////////////////////////////	
								$sql = "UPDATE members SET  LOOP_STATUS = 'COMPLETE', CURRENT_PACKAGE = 'NONE', FLOW_DIRECTION = 'NONE', LOH_STATUS = 'PENDING', RECYCLING_DEADLINE = ?  WHERE USERNAME = ?   LIMIT 1";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($recyl_deadline,$username));
								
								$alert2 = '<div id="green" class="errors">CONFIRMATION SUCCESSFUL<br/>Your Current '.$curr_package.' Loop Cycle is now complete, You can now start a new loop<br/>REMEMBER TO SUBMIT YOUR TESTIMONIAL WITHIN 3 DAYS ELSE YOUR ACCOUNT WILL BE BLOCKED.</div>';
								
							}///////IF RECEIVING FIRST DONATION/////////////////////////////////////////////////					
							else{
								
								///////////NOW RECORD THE FIRST DONATION TRANSACTION IN THE TRANSACTION TABLE FOR THE RECEIVER////////////////////////////////////////////////////////////				
								
								$desc = 'RECEIVED';
								$trans_time = time();
								
								/****RECEIVER*********/
								///////////PDO QUERY////////////////////////////////////	
						
								$sql = "UPDATE transactions SET DONATION1=?, DONATION1_TIME=?, STATUS = 'SEMI-SUCCESSFUL' WHERE DONATION_ID=? ";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($desc,$trans_time,$rec_did));
								
								
								$alert2 = '<div id="green" class="errors blink">CONFIRMATION SUCCESSFUL</div>';
							}
						}
						else{
								$alert2 = '<div id="red" class="errors blink">CONFIRMATION FAILED<br/>SOMETHING WENT WRONG</div>';
							}
					}
					else{
						$alert2 = '<div class="errors blink">INCORRECT ACCOUNT VERIFICATION NUMBER(AVN),<br/> Please try again</div>';
					}
					
					
					////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
					echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert2)."')</script>";
					
				}
				
				
		}
		else{
			$alert = '<div class="errors blink">YOUR CYCLE IS COMPLETE<br/>YOU HAVE TO BEGIN A NEW CYCLE OR YOUR ACCOUNT WILL BE BLOCKED  AS SOON AS YOUR RECYCLING DEADLINE ELAPSES.</div>';
		}
		
		
		
		
	
	/***************GET DECLINATION, PURGE COUNTER, RECYCLING DEADLINE AND COMMENTS FOR THE USER*********************************************************/
	
	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT TOTAL_DECL, TOTAL_PURGE, RECYCLING_DEADLINE, COMMENT1, COMMENT2, OT_AVN, SUSPENSION_STATUS FROM members WHERE USERNAME = ?  LIMIT 1";

		$stmt = $pdo_conn_login->prepare($sql);
		$stmt->execute(array($username));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$total_decl = $row["TOTAL_DECL"];
		$total_purge = $row["TOTAL_PURGE"];
		$comments = $row["COMMENT1"];
		$comments .= $row["COMMENT2"];
		$ot_avn = $row["OT_AVN"];		
		$suspension_stat = $row["SUSPENSION_STATUS"];
		
		if($suspension_stat == "YES"){
			header("location:logout");
			exit();
		}
		
		if($comments){
			$comments = '<div class="errors">'.$comments.'<br/>							
								<input  type="button"  class="formButtons clear_comm" id="'.$pack_btn_css.'" value="OK" />											
						</div>';
		}
		if($ot_avn){
			$ot_avn = '<div class="errors">WELCOME <a class="links" href="edit-profile">'.strtoupper($username).'</a><br/>YOUR ACCOUNT VERIFICATION NUMBER(AVN) IS:<br/><b class="green">'.$ot_avn.'</b><br/>							
								Please copy it and keep it safe because you will need it for all your transactions and thereafter hit the HIDE button to protect your AVN<br/>Thank You<br/>
								<input  type="button"  class="formButtons hide_avn" id="'.$pack_btn_css.'" value="HIDE" />	
								<br/><br/>Wondering About Where to Fill Out Your Bank Account Details?<br/> click <a class="links" href="edit-profile">here now</a> to edit your profile and fill out your bank details.
						</div>';
		}
		
		$recyl_deadline = $row["RECYCLING_DEADLINE"];
		
		$declinations = $total_decl;
		
		if($total_decl)
			$total_decl = 'TOTAL DECLINATION: '.$total_decl.'<br/>';
		else
			$total_decl = '';
		if($total_purge)
			$total_purge = 'TOTAL PURGE: '.$total_purge;
		else
			$total_purge = '';
		
		if($total_decl || $total_purge)
			$alert3 = $total_decl.$total_purge.'<hr/>';
		
		
		
		
		
	/*********************CHECK AGAIN IF THE USER HAS ACTIVE OR INCOMPLETE PACKAGE LOOP AWAITING********************************************/
	
	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT CURRENT_PACKAGE, FLOW_DIRECTION FROM members WHERE USERNAME = ? AND CURRENT_PACKAGE !='NONE'  LIMIT 1";

		$stmt1 = $pdo_conn_login->prepare($sql);
		$stmt1->execute(array($username));
		$chk_row = $stmt1->fetch(PDO::FETCH_ASSOC);
		$active_package = $stmt1->rowCount();
		
		if($active_package){/////////IF THERE IS AN ACTIVE PACKAGE THEN SHOW THE USER MATCH RELATED INFOS////////////////////////////////////
				
				$curr_package = $chk_row["CURRENT_PACKAGE"];
				$flow_dir = $chk_row["FLOW_DIRECTION"];
				$donation_table = 'euro_'.strtolower($curr_package).'_donations';
				$matching_table = 'euro_'.strtolower($curr_package).'_matching';
				
				
				switch($curr_package){///////DETERMINE THE INCOMING DONATION AMOUNT AND CSS CLASS FOR THE PACKAGE//////////////////////////////////////////////////////
					
					case "STANDARD":{$inc_amt = 5000; $pack_css = 'std_pack'; $pack_btn_css = 'std_btn'; break;}
					case "CLASSIC":{$inc_amt = 10000; $pack_css = 'clsc_pack'; $pack_btn_css = 'clsc_btn'; break;}
					case "PREMIUM":{$inc_amt = 20000; $pack_css = 'prm_pack'; $pack_btn_css = 'prm_btn'; break;}
					case "ELITE":{$inc_amt = 50000; $pack_css = 'elt_pack'; $pack_btn_css = 'elt_btn'; break;}
					case "LORD":{$inc_amt = 100000; $pack_css = 'lrd_pack'; $pack_btn_css = 'lrd_btn'; break;}
					case "MASTER":{$inc_amt = 200000; $pack_css = 'mst_pack'; $pack_btn_css = 'mst_btn'; break;}
					case "ROYAL":{$inc_amt = 500000; $pack_css = 'roy_pack'; $pack_btn_css = 'roy_btn'; break;}
					case "ULTIMATE":{$inc_amt = 1000000; $pack_css = 'ult_pack'; $pack_btn_css = 'ult_btn'; break;}
					
				}
				
				
				$trx_num = getCurrentOrderNumber();
				
			//////////CHECK IF THE USER HAS BEEN MATCHED///////////////////////////////////////////////////////
			
			if($flow_dir == "OUT"){/////////WHEN MAKING DONATIONS////////////////////////
			
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "SELECT ID,AMOUNT_PLEDGED FROM ".$donation_table." WHERE USERNAME = ? AND (MATCH_STATUS = 'MATCHED' OR MATCH_STATUS = 'SEMI-MATCHED')  AND (LOOP_STATUS ='COMPLETE' AND LOOP_STATUS !='DECLINED')   LIMIT 1";
				$stmt2 = $pdo_conn_login->prepare($sql);
				$stmt2->execute(array($username));
				if($stmt2->rowCount()){///////////////IF THE USER HAS BEEN MATCHED//////////////////////////////////////
					
					$id_row = $stmt2->fetch(PDO::FETCH_ASSOC);					
					$payer_did = $id_row["ID"];
					$amount_pledged = $id_row["AMOUNT_PLEDGED"];
					
				//////////GET THE PERSON YOU HAVE BEEN MERGED TO PAY IF YOU HAVE'NT DECLINED THE MATCH////////////////////////////////////////////////////////////////////////
						
				///////////PDO QUERY////////////////////////////////////	
					
					$sql = "SELECT * FROM ".$matching_table." WHERE PAYER_USERNAME = ? AND PAYER_DID = ?  AND PAID_OR_DECLINED !='DECLINED'  LIMIT 1";
					$stmt3 = $pdo_conn_login->prepare($sql);
					$stmt3->execute(array($username, $payer_did));
					$match_row = $stmt3->fetch(PDO::FETCH_ASSOC);
					
					$receiver = $match_row["REC_USERNAME"];
					$payer_deadline = $match_row["PAYER_DEADLINE"];
					$payment_method = $match_row["METHOD_OF_PAY"];
					$payment_name = $match_row["PAYMENT_SLIP_NAME"];
					$uploaded_pop = $match_row["UPLOADED_PROOF"];	
					$time_paid = $match_row["TIME_OF_PAY"];		
					
									
					///////////GET ALL THE CURRENT RECEIVER'S DETAILS //////////////
					/////////PDO QUERY////////////////////////////////////	
		
					$sql = "SELECT * FROM members  WHERE USERNAME = ? LIMIT 1";

					$stmt4 = $pdo_conn_login->prepare($sql);
					$stmt4->execute(array($receiver));
					if($stmt2->rowCount()){
						$rec_detail = $stmt4->fetch(PDO::FETCH_ASSOC);
						$rec_email = $rec_detail["EMAIL"];
						$rec_phone = $rec_detail["MOBILE_PHONE"];
						$rec_alt_phone = $rec_detail["ALT_MOBILE_PHONE"];
						$rec_acc_name = $rec_detail["ACCOUNT_NAME"];
						$rec_bnk_name = $rec_detail["BANK_NAME"];
						$rec_acc_num = $rec_detail["ACCOUNT_NUMBER"];
						$rec_fn = $rec_detail["FULL_NAME"];
						$rec_avatar = getDP($receiver,"NOLINK");
						$rec_gender = $rec_detail["GENDER"];
					
					}
					
					/////////IF A PAYER HAS PAID AND SENT POP/////////////////////////////////////////////////////////
						
					if($payment_method && $payment_name && $uploaded_pop){
						
						$alert = '<h1 class="h_bkg2">NEW ORDER - '.$trx_num.' (Provide Help)</h1>
									<div class="packages '.$pack_css.'">
										<h1>AWAITING CONFIRMATION FROM:</h1>
										<h5 class="clear">'.$rec_avatar.'</h5>
										<h3><span>USERNAME:</span> '.$receiver.'</h3>
										<h3><span>NAME:</span> '.$rec_fn.'</h3>
										<h3><span>EMAIL:</span> <a class="links" href="mailto:'.$rec_email.'">'.$rec_email.'</a></h3>
										<h3><span>PHONE 1:</span> '.$rec_phone.'</h3>
										<h3><span>PHONE 2:</span> '.$rec_alt_phone.'</h3>
										<form method="post" target="_blank" action="send-pm">											
											<input type="hidden" name="receiver" value="'.$receiver.'" />
											<input type="submit"  name="m2m_pm" class="formButtons" id="'.$pack_btn_css.'" value="SEND PM" />											
										</form>	<hr/>										
										<h3><span>AMOUNT DISBURSED:</span> ₦'.formatNumber($amount_pledged).'</h3>																													
										<h3><span>TIME DISBURSED:</span> '.dateFormatStyle($time_paid).'</h3><hr/>																																							
										<form method="post" target="_blank" action="make-report" >													
												<input type="hidden" name="offender" value="'.$receiver.'" />													
											<input type="submit"  name="make" style="background:red;" class="formButtons" id="'.$pack_btn_css.'" value="REPORT" />
										</form>										
								 </div>';
					}
					else{/////////IF A PAYER HAS NOT PAID OR SENT POP/////////////////////////////////////////////////////////
						
						$alert = '	<div class="errors">
										ATTENTION<br/>PLEASE DISBURSE ONLY THE AMOUNT SPECIFIED IN THIS ORDER BELOW TO EXACTLY THE BANK ACCOUNT DETAILS SPECIFIED.
									</div>
									<h1 class="h_bkg2">NEW ORDER - '.$trx_num.' (Provide Help)</h1>
									<div class="packages '.$pack_css.'">
										<h1>PLEASE DISBURSE TO:</h1>
										<h5 class="clear">'.$rec_avatar.'</h5>
										<h3><span>USERNAME:</span> '.$receiver.'</h3>
										<h3><span>NAME:</span> '.$rec_fn.'</h3>										
										<h3><span>EMAIL:</span> <a class="links" href="mailto:'.$rec_email.'">'.$rec_email.'</a></h3>
										<h3><span>PHONE 1:</span> '.$rec_phone.'</h3>
										<h3><span>PHONE 2:</span> '.$rec_alt_phone.'</h3>																		
										<form method="post" target="_blank" action="send-pm">											
											<input type="hidden" name="receiver" value="'.$receiver.'" />
											<input type="submit"  name="m2m_pm" class="formButtons" id="'.$pack_btn_css.'" value="SEND PM" />											
										</form>
										<h2 class="h_bkg">BANK DETAILS</h2>		
										<h3><span>BANK NAME:</span> '.$rec_bnk_name.'</h3>										
										<h3><span>ACCOUNT NUMBER:</span> '.$rec_acc_num.'</h3>
										<h3><span>ACCOUNT NAME:</span> '.$rec_acc_name.'</h3>										
										<hr/>													
										<h2 class="pay_timer_wrapper"></h2>
										<h3><span>OUTGOING DONATION:</span> ₦'.formatNumber($amount_pledged).'</h3>
										<hr/>
										<input type="button" class="formButtons have_paid" id="'.$pack_btn_css.'" value="I HAVE PAID" />										
										<span></span>
										<div class="modal">											
											<div class="modal_content">
												<div class="modal_header clear">PAYMENT DETAILS <span class="close_modal">&times;</span></div>
												<form method="post" name="have_paid_form" action="dash-board" enctype="multipart/form-data">
													<fieldset>
														<label>Method Of Payment<span class="red">*</span></label>												
														<input type="text" required placeholder="method of payment" name="pay_method" value="" class="only_form_textarea_inputs" />
														<label>Name Used<span class="red">*</span></label>												
														<input type="text" required placeholder="Name used to pay" name="slip_name" value="" class="only_form_textarea_inputs" />
														<label>Proof Of Payment(Max-size:5MB)<span class="red">*</span>(<span class="red">JPEG/PNG ONLY</span>)</label>												
														<input type="file" required accept="image/*"  name="proof" value="" class="only_form_textarea_inputs" />
														<input type="hidden" name="payer_did" value="'.$payer_did.'" />
													</fieldset>
													<input type="submit"  name="have_paid" class="formButtons" id="'.$pack_btn_css.'" value="SUBMIT PROOF" />
												</form>	
											</div>
										</div>
										<input type="button" class="formButtons decline_pay" id="'.$pack_btn_css.'" value="I CANNOT PAY" />										
										<span></span>
										<div class="modal">																						
											<div class="modal_content">
												<div class="modal_header clear">DECLINE PAYMENT<span class="close_modal">&times;</span></div>
												<div class="red">ATTENTION!!! YOUR ACCOUNT WILL BE BLOCKED AFTER TWO PAYMENT DECLINATIONS<br/><br/>YOU HAVE (<b>'.$declinations.'</b>) DECLINATION SO FAR<br/><br/>ARE YOU SURE YOU WANT TO STILL DECLINE THIS PAYMENT?</div>
												<form name="decline_pay_form" method="post" action="dash-board">
													<input type="hidden" name="payer_did" value="'.$payer_did.'" />
													<input type="submit"  name="decline_pay" class="formButtons decline_pay" id="'.$pack_btn_css.'" value="YES" />
													<input type="button"  class="formButtons close_modal" id="'.$pack_btn_css.'" value="NO" />
												</form>
											</div>
										</div>
									</div>';
					}
						
				}
				else{///////////////IF THE USER HAS NOT BEEN MATCHED//////////////////////////////////////
					$alert = '<h1 class="h_bkg2">NEW ORDER - '.$trx_num.' (Provide Help)</h1>
								<div class="packages '.$pack_css.'"><h1>AWAITING MATCH</h1><h3><span>OUTGOING DONATION:</span> ₦'.formatNumber($inc_amt).'  </h2></div>';
				}
			}
			elseif($flow_dir == "IN"){////////WHEN GETTING DONATIONS/////////////////////////
			
				//////////IF REDIRECTED FROM REFERRAL CASHOUT /////////////////////////////
				if(isset($_GET["rrc"]) && $_GET["rrc"] == 1)
					$cashout_alert = '<div id="green" class="errors">YOUR REQUEST TO CASHOUT YOUR REFERRAL REWARDS HAS BEEN DISPATCHED 
										SUCCESSFULLY. PLEASE WAIT, YOU WILL BE MERGED TO RECEIVE PAYMENT SHORTLY </div>';
						
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "SELECT ID FROM ".$donation_table." WHERE USERNAME = ? AND MATCH_STATUS IN ('MATCHED', 'SEMI-MATCHED', 'SEMI-MATCHED-PURGED', 'AWAITING-AND-PURGED') AND (LOOP_STATUS = 'SEMI-COMPLETE' AND LOOP_STATUS !='DECLINED')   LIMIT 1";
				$stmt2 = $pdo_conn_login->prepare($sql);
				$stmt2->execute(array($username));
				if($stmt2->rowCount()){///////////////IF THE USER HAS BEEN MATCHED//////////////////////////////////////
										
					$id_row = $stmt2->fetch(PDO::FETCH_ASSOC);					
					$rec_did = $id_row["ID"];
					
					
					
				/////////////////GET THE TWO MATCHES THAT WILL PAY THE CURRENT USER WHERE THEY HAVE'NT DECLINED TO PAY////////////////
				///////////PDO QUERY////////////////////////////////////	
					
					$sql = "SELECT * FROM ".$matching_table." WHERE (REC_USERNAME = ? AND REC_DID = ?) AND (PAID_OR_DECLINED !='DECLINED'  AND CONFIRMED != 'YES' AND CONFIRMED != 'DECLINED')  LIMIT 2";
					$stmt3 = $pdo_conn_login->prepare($sql);
					$stmt3->execute(array($username, $rec_did));
					$payers_found = $stmt3->rowCount();
					
					/*******CHECK IF THE USER HAS BEEN MATCHED TO RECEIVE A SECOND TIME**************/
					
					if($payers_found){
						
						while($match_row = $stmt3->fetch(PDO::FETCH_ASSOC)){
							
							$payer_did = $match_row["PAYER_DID"];
							$payer = $match_row["PAYER_USERNAME"];
							$payment_method = $match_row["METHOD_OF_PAY"];
							$payment_name = $match_row["PAYMENT_SLIP_NAME"];
							$uploaded_pop = $match_row["UPLOADED_PROOF"];						
							$time_paid = $match_row["TIME_OF_PAY"];						
											
							///////////GET ALL THE CURRENT PAYER'S DETAILS //////////////
							/////////PDO QUERY////////////////////////////////////	
				
							$sql = "SELECT * FROM members  WHERE USERNAME = ? LIMIT 1";

							$stmt4 = $pdo_conn_login->prepare($sql);
							$stmt4->execute(array($payer));
							if($stmt4->rowCount()){
								$payer_detail = $stmt4->fetch(PDO::FETCH_ASSOC);
								$payer_email = $payer_detail["EMAIL"];
								$payer_phone = $payer_detail["MOBILE_PHONE"];
								$payer_alt_phone = $payer_detail["ALT_MOBILE_PHONE"];
								$payer_acc_name = $payer_detail["ACCOUNT_NAME"];
								$payer_bnk_name = $payer_detail["BANK_NAME"];
								$payer_acc_num = $payer_detail["ACCOUNT_NUMBER"];
								$payer_fn = $payer_detail["FULL_NAME"];
								$payer_avatar = getDP($payer,"NOLINK");
								$payer_gender = $payer_detail["GENDER"];
							}
							
							
							/////////IF A PAYER HAS PAID AND SENT POP/////////////////////////////////////////////////////////
							
							if($payment_method && $payment_name && $uploaded_pop){
									
								$alert .= ' <h1 class="h_bkg2"> ORDER - '.$trx_num.' (Get Help)</h1>
											<div class="packages '.$pack_css.'">
												<h1>AWAITING CONFIRMATION FROM YOU:</h1>
												<h5 class="clear">'.$payer_avatar.'</h5>
												<h3><span>USERNAME:</span> '.$payer.'</h3>
												<h3><span>FULL NAME:</span> '.$payer_fn.'</h3>										
												<h3><span>EMAIL:</span> <a class="links" href="mailto:'.$payer_email.'">'.$payer_email.'</a></h3>
												<h3><span>PHONE 1:</span> '.$payer_phone.'</h3>
												<h3><span>PHONE 2:</span> '.$payer_alt_phone.'</h3>
												<form method="post" target="_blank" action="send-pm">											
													<input type="hidden" name="receiver" value="'.$payer.'" />
													<input type="submit"   name="m2m_pm" class="formButtons" id="'.$pack_btn_css.'" value="SEND PM" />											
												</form>	<hr/>
												<h3><span>AMOUNT DISBURSED:</span> ₦'.formatNumber($inc_amt).'</h3>
												<h3><span>PAYMENT WAS MADE:</span> '.dateFormatStyle($time_paid).'</h3><hr/>
												<div class="pop_details">
													<h1>PROOF OF PAYMENT SENT</h1>
													<h4><span>PAYMENT METHOD: </span>'.$payment_method.'</h4>							
													<h4><span>NAME USED: </span>'.$payment_name.'</h4>
													<h4><span>UPLOADED PROOF:<br/></span><img class="pop" alt="pop" src="wealth-island-uploads/proof_of_payments/'.$uploaded_pop.'"/></h4>
												</div><hr/>										
												<input type="button" class="formButtons confirm_paid" id="'.$pack_btn_css.'" value="CONFIRM" />
												<span></span>
												<div class="modal">											
													<div class="modal_content">
														<div class="modal_header clear">DISBURSEMENT RECEIPT CONFIRMATION  <span class="close_modal">&times;</span></div>
														<span class="red">ATTENTION!!! Please ensure that the disbursed money is already in your account before confirming this member.<br/>
														ARE YOU SURE YOU HAVE RECEIVED DISBURSEMENT FROM THIS MEMBER</span>
														<form method="post" action="dash-board">
															<label>AVN<span class="red">*</span></label>
															<input required autocomplete="off" placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
															<input type="hidden" name="rec_did" value="'.$rec_did.'" />
															<input type="hidden" name="payer_did" value="'.$payer_did.'" /><br/>
															<input type="submit"  name="final_confirm" class="formButtons" id="'.$pack_btn_css.'" value="YES AM SURE" />
															<input type="button"  class="formButtons close_modal" id="'.$pack_btn_css.'" value="NOT YET" />
														</form>																							
													</div>
												</div>																			
												<form method="post" target="_blank" action="make-report" >													
														<input type="hidden" name="offender" value="'.$payer.'" />													
													<input type="submit"  name="make" class="formButtons" style="background:red;" id="'.$pack_btn_css.'" value="REPORT" />
												</form>	
												<hr/>
												<div class="errors">ATTENTION!!! Only click on the PURGE button when you are sure a fake proof of payment was uploaded when no actual payment was made or received.<br/><br/>NOTE WHEN YOU PURGE A USER, THE CASE WILL BE REVIEWED BY THE ADMINISTRATORS BEFORE YOU CAN BE RE-MATCHED.</div>
												<input type="button" title="Only click on this button when you are sure a fake proof of payment was uploaded when no actual payment was made or received. " class="formButtons purge" style="background:red;" id="'.$pack_btn_css.'" value="PURGE" />
												<span></span>
												<div class="modal">													
													<div class="modal_content">
														<div class="modal_header clear">PURGE USER<span class="close_modal">&times;</span></div>														
														<b class="red">ARE YOU SURE?</b>
														<form method="post" action="dash-board" >													
																<input type="hidden" name="payer_did" value="'.$payer_did.'" />													
															<input type="submit"  name="purge" title="Only click on this button when you are sure a fake proof of payment was uploaded when no actual payment was made or received." class="formButtons" style="background:red;" id="'.$pack_btn_css.'" value="YES" />
															<input type="button"   class="formButtons close_modal" style="background:red;" id="'.$pack_btn_css.'" value="CANCEL" />
														</form>	
													</div>
												</div>
											</div>';
							}
							else{////IF A PAYER HAS'NT PAID OR SENT POP/////////////////////////////////////////////
									
								$alert .= '<h1 class="h_bkg2">ORDER - '.$trx_num.' (Get Help)</h1>
											<div class="packages '.$pack_css.'">
												<h1>AWAITING PAYMENT FROM:</h1>
												<h5 class="clear">'.$payer_avatar.'</h5>
												<h3><span>USERNAME:</span> '.$payer.'</h3>
												<h3><span>FULL NAME:</span> '.$payer_fn.'</h3>
												<h3><span>INCOMING DONATION:</span> ₦'.formatNumber($inc_amt).'</h3>
												<h3><span>EMAIL:</span> <a class="links" href="mailto:'.$payer_email.'">'.$payer_email.'</a></h3>
												<h3><span>PHONE 1:</span> '.$payer_phone.'</h3>
												<h3><span>PHONE 2:</span> '.$payer_alt_phone.'</h3>	
												<form method="post" target="_blank" action="send-pm">											
													<input type="hidden" name="receiver" value="'.$payer.'" />
													<input type="submit"  name="m2m_pm" class="formButtons" id="'.$pack_btn_css.'" value="SEND PM" />											
												</form>	
										  </div>';
							}
							
						
						}
					}
					else{///////////////IF THE USER HAS NOT BEEN MATCHED TO RECEIVE THE REMAINING //////////////////////////////////////
					$alert = ' <h1 class="h_bkg2">NEW ORDER - '.$trx_num.' (Get Help)</h1>
								<div class="packages '.$pack_css.'"><h1>AWAITING MATCH</h1><h3><span>INCOMING DONATION:</span> ₦'.formatNumber($inc_amt).'  </h3></div>';
					
					}

				}
				else{///////////////IF THE USER HAS NOT BEEN MATCHED//////////////////////////////////////
					$alert = '<h1 class="h_bkg2">NEW ORDER - '.$trx_num.' (Get Help)</h1>
							  <div class="packages '.$pack_css.'"><h1>AWAITING MATCH</h1><h3><span>INCOMING DONATION:</span> ₦'.formatNumber($inc_amt).'  </h3></div>
							  <div class="packages '.$pack_css.'"><h1>AWAITING MATCH</h1><h3><span>INCOMING DONATION:</span> ₦'.formatNumber($inc_amt).'  </h3></div>';				
				}
			}
			
		}
		else{
			$alert = '<div class="errors blink">YOUR CYCLE IS COMPLETE<br/>YOU HAVE TO BEGIN A NEW CYCLE OR YOUR ACCOUNT WILL BE BLOCKED AS SOON AS YOUR RECYCLING DEADLINE ELAPSES.</div>';
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

<title>DASHBOARD</title>
<?php require_once('include-html-headers.php')   ?>


<?php 

	if($recyl_deadline && !$active_package) 
		echo '<script>startCountDown('.$recyl_deadline.')</script>';
	if(isset($payer_deadline) && $payer_deadline )
		echo '<script>startPayerCountDown('.$payer_deadline.')</script>';
		
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

	
	/**********CALL FUNCTION TO DO FIRST AND SECOND MATCH*****************************************/
	//echo '<script>doFirstMatch()</script>';
	//echo '<script>doSecondMatch()</script>';
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
		
		echo "<a href='dash-board' title=>DASHBOARD</a> "  ;
				
		?>
	</header>	

	<div class="view_user_wrapper" id="hide_vuwbb">
				
		<?php echo getMidPageScroll(); ?>	
	
	<?php
		
		if(isset($alert2)) echo $alert2; 
		if(isset($ot_avn)) echo $ot_avn;
		if(isset($cashout_alert)) echo $cashout_alert; 
		if(isset($alert3)) echo '<div class="errors blink">'.$alert3.'</div>'; 
		if(isset($alertinbox)) echo '<div class="errors blink">'.$alertinbox.'</div>'; 
		if($comments) echo $comments;
		if(isset($curr_package) && $curr_package)
			echo '<div class="'.$pack_css.'"><h1>CURRENT PACKAGE: '.$curr_package.'</h1></div>';
		echo getLatestNews();
		
		if(isset($alert)) echo $alert;
	
	?>
		<div class="timer_wrapper"></div>
	<?php if(!$active_package){ ?>
	
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
	
	<?php } ?>
		
		
	</div>
	<?php require_once('eurofooter.php')     ?>
</div>
</body>

</html>


