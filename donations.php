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

setPageTimeZone();

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sort_order="";$datas = "";$page_id="";$page_id_out="";$start_rec=""; $pagination="";$pagination_right="";$pagination_left="";
$total_page="";$curr_page="";$tab_options = "";$tname="";$get_sort="";$trx_num_srch="";



$username = $_SESSION["username"];

if(getUserPrivilege($username) == 'ADMIN'){

	if($username){	
	
			
		$page_self = getReferringPage("qstr url");
		//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////
		
		if(isset($_COOKIE["form_gate_response"])){
			
			$alert2 = $_COOKIE["form_gate_response"];
			
				
		/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

				setcookie("form_gate_response", "", (time() -  1800));

		}

		
/********************************RUN PURGER REMATCH*********************************************/		
		if(isset($_POST["run_purger_rematch"])){
						
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
									$alert2 = '<div id="green" class="errors blink">YOUR REMATCH FOR TARGET DID = '.$did.'  HAS BEEN SET SUCCESSFULLY!!!</div>';
								else
									$alert2 = '<div  class="errors blink">YOUR REQUEST TO SET REMATCH FOR TARGET DID = '.$did.'  HAS FAILED!!!</div>';
									
								
							}///////IF THE PURGE WAS TRIGGERED FROM FIRST MATCH(SEMI-MATCHED), 
							////THEN REMATCH RECEIVER BY STANDARD TWO MATCH BY SETTING MATCH_STATUS TO AWAITING ///////////////							
							elseif($match == "MATCH-BY-TWO"){
								
								$match_by = 'AWAITING';
							///////////PDO QUERY////////////////////////////////////						
								$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = ?  WHERE ID = ?   LIMIT 1";
								$stmt = $pdo_conn_login->prepare($sql);
								if($stmt->execute(array($match_by, $did)))
									$alert2 = '<div id="green" class="errors blink">YOUR REMATCH FOR TARGET DID = '.$did.'  HAS BEEN SET SUCCESSFULLY!!!</div>';
								else
									$alert2 = '<div  class="errors blink">YOUR REQUEST TO SET REMATCH FOR TARGET DID = '.$did.' HAS FAILED!!!</div>';
									
							}
								
						
					}
					else{
							$alert2 = '<div class="errors blink">INCORRECT ACCOUNT VERIFICATION NUMBER(AVN),<br/> Please try again</div>';
					}
				}
				else{
					$alert2 = '<div class="errors blink">Please fill out all the fields</div>';
				}
					
					
					////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
					echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert2)."')</script>";
						
			
			
		}
		
		

			
/**********************************FCFM FORCED CONFIRMATION**************************************************/
					
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
	
					$sql = "SELECT USERNAME,AMOUNT_PLEDGED,PACKAGE FROM ".$donation_table." WHERE ID = ?  LIMIT 1";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array($rec_did));
					$rec_row = $stmt->fetch(PDO::FETCH_ASSOC);
					
					$target_package = $rec_row["PACKAGE"];
					$receiver = $rec_row["USERNAME"];
					$amt_pledged = $rec_row["AMOUNT_PLEDGED"];
					if(getUserPrivilege($username) == "ADMIN")
						$user = $username;										
										
					if(verifyAVN($user, $avn)){
						
					/***************CONFIRM THE PAYER AND COMPLETE THE RECEIVER'S  LOOP ACCORDINGLY********************************************************************/
						
					//////////////FIRST FETCH THE PAYER DETAILS FROM THE MATCHING TABLE WHERE PAYER MADE PAYMENT////////////////////////////////////////////////////////
					
					///////////PDO QUERY////////////////////////////////////	
						
						$sql = "SELECT PAYER_DID,PAYER_USERNAME FROM ".$matching_table." WHERE REC_DID = ? AND PAYER_DID = ?  AND CONFIRMED != 'YES' AND UPLOADED_PROOF != ''  LIMIT 1";

						$stmt = $pdo_conn_login->prepare($sql);
						$stmt->execute(array($rec_did,$payer_did));
						$row = $stmt->fetch(PDO::FETCH_ASSOC);					
						$payer_did = $row["PAYER_DID"];
						$payer = $row["PAYER_USERNAME"];
						$confirm_time = time();
						
						if($stmt->rowCount()){							
							
							/*******PAYER HANDLES***********/
							
							////////////////NOW CONFIRM THE PAYER IN DONATION AND MATCHING TABLE AND SET HIS MATCH_STATUS TO/////////////
							///////// AWAITING SO HE CAN BE MATCHED TO RECEIVE //////////
							////////////IMPORTANT: SET THE PAID_OR_DECLINED = "PAID" (AGAIN) JUST INCASE THE PAYER WAS PURGED UNFAIRLY ////////////////////
							///////////PDO QUERY////////////////////////////////////	
							
							$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = 'AWAITING',  LOOP_STATUS = 'SEMI-COMPLETE', PAID_OR_DECLINED = 'PAID', CONFIRMED = 'YES', CONFIRM_TIME = ?  WHERE ID = ?   LIMIT 1";
							
							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($confirm_time,$payer_did));///////////PDO QUERY////////////////////////////////////	
							
							////////////////ALSO CONFIRM THE PAYER IN MATCHING TABLE ////////////////////
							///////////PDO QUERY////////////////////////////////////	
							$sql = "UPDATE ".$matching_table." SET  PAID_OR_DECLINED= 'PAID', CONFIRMED = 'YES', CONFIRM_TIME = ?  WHERE PAYER_DID = ?   LIMIT 1";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($confirm_time,$payer_did));
							
							///////////IMPORTANT SET CURRENT_PACKAGE TO TARGET PACKAGE INCASE OF AN UNFAIRLY PURGED USER///////////////
							/////////////////SO AS TO COUNTER THE CURRENT_PACKAGE = 'NONE' SET DURING PURGE TIME. ///////////////////////////////////
							////////////////ALSO UPDATE THE PAYER'S LOOP_STATUS = 'SEMI-COMPLETE' AND FLOW_DIRECTION = 'IN' IN MEMBERS TABLE ////////////////////
							///////////PDO QUERY////////////////////////////////////	
							$sql = "UPDATE members SET  CURRENT_PACKAGE = ?, LOOP_STATUS = 'SEMI-COMPLETE', FLOW_DIRECTION = 'IN', COMMENT1 = 'YOU HAVE BEEN CONFIRMED AND AWAITING MATCH TO RECEIVE' WHERE USERNAME = ?   LIMIT 1";
							
							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($target_package, $payer));
							
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
							
														
							////////////////UPDATE THE PAYER IN TRANSACTION TABLE ////////////////////														
							////INCASE OF AN UNFAIRLY PURGED USER RESET DONATION1='PENDING',DONATION2='PENDING',////////
							///////////////DONATION1_TIME=0, DONATION2_TIME=0 TO COUNTER THEIR DECLINED VALUES SET DURING PURGE///////////////////////////////////////////////////////
							/****PAYER*********/
							///////////PDO QUERY////////////////////////////////////	
					
							$sql = "UPDATE transactions SET STATUS='PAID', DONATION1='PENDING', DONATION1_TIME=0, DONATION2='PENDING', DONATION2_TIME=0  WHERE DONATION_ID=? ";

							$stmt = $pdo_conn_login->prepare($sql);
							$stmt->execute(array($payer_did));
							
							///////////IF THE PAYER WAS PURGED UNFAIRLY THEN RESET HIS PURGE COUNTER IN MEMBERS AND PURGES TABLE/////////////////////////////////////////////////////
							if(isset($_POST["unfairly_purged"])){
									
								///////////PDO QUERY////////////////////////////////////	
								$sql = "UPDATE members SET  TOTAL_PURGE = 0  WHERE USERNAME = ?   LIMIT 1";
								
								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($payer));
								
								///////////PDO QUERY////////////////////////////////////	
								$sql = "UPDATE purges SET  TOTAL = 0  WHERE USERNAME = ?   LIMIT 1";
								
								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($payer));						
								
							
							}
							
							

							
							
							/************HANDLE RECEIVERS ACCORDINGLY*******************************************/							
							
							///////////////////IF ADMIN WISHES TO FORCE CONFIRM A DONATION ////
							/////////(NOT THAT THE USER WAS PURGED)//////////////////////////////////
							if(!isset($_POST["unfairly_purged"])){
								
									
								/*******RECEIVER***********/
								////////////////UPDATE THE RECEIVER COUNTER IN DONATION TABLE ////////////////////
								///////////PDO QUERY////////////////////////////////////	
								$sql = "UPDATE ".$donation_table." SET REC_COUNTER = (REC_COUNTER + 1)  WHERE ID = ?   LIMIT 1";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($rec_did));								
								
							
								////////////////CHECK IF THE TWO ASSIGNED MEMBERS HAS PAID COMPLETELY THEN////////
								////////// COMPLETE THE RECEIVER'S LOOP CYCLE ACCORDINGLY ////////////////////
								///////////PDO QUERY////////////////////////////////////	
								$sql = "SELECT REC_COUNTER FROM ".$donation_table."  WHERE ID = ? AND REC_COUNTER = 2   LIMIT 1";

								$stmt = $pdo_conn_login->prepare($sql);
								$stmt->execute(array($rec_did));
								
								///////IF RECEIVING SECOND DONATION/////////////////////////////////////////////////					
								if($stmt->rowCount()){
								
									////////////////FINALLY  COMPLETE THE RECEIVER'S LOOP CYCLE IN DONATION TABLE ////////////////////
									///////////PDO QUERY////////////////////////////////////	
									$sql = "UPDATE ".$donation_table." SET  LOOP_STATUS = 'COMPLETE', MATCH_STATUS = 'MATCHED-COMPLETE', AMOUNT_MATCHED = RETURN_AMOUNT, AMOUNT_REM = 0  WHERE ID = ?   LIMIT 1";

									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($rec_did));
																											
									///////////NOW RECORD THE SECOND DONATION TRANSACTION IN THE TRANSACTION TABLE FOR THE RECEIVER////////////////////////////////////////////////////////////				
									
									$desc = 'RECEIVED';
									$trans_time = time();
									
									/****RECEIVER*********/
									///////////PDO QUERY////////////////////////////////////	
							
									$sql = "UPDATE transactions SET DONATION2=?, DONATION2_TIME=?, STATUS = 'SUCCESSFUL'  WHERE DONATION_ID=? ";

									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($desc,$trans_time,$rec_did));
									
									/*********SET RECYCLING DEADLINE******************/
									$recyl_deadline = getRecyclingDeadline();
									
									////////////////ALSO COMPLETE THE RECEIVER'S LOOP CYCLE AND SET NEW RECYCLING DEADLINE IN MEMBERS TABLE ////////////////////
									///////////PDO QUERY////////////////////////////////////	
									$sql = "UPDATE members SET  LOOP_STATUS = 'COMPLETE', CURRENT_PACKAGE = 'NONE', FLOW_DIRECTION = 'NONE', LOH_STATUS = 'PENDING', RECYCLING_DEADLINE = ?  WHERE USERNAME = ?   LIMIT 1";

									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($recyl_deadline,$receiver));
									
									$alert2 = '<div id="green" class="errors blink">CONFIRMATION SUCCESSFUL<br/>The Current target receiver has now completed his '.$target_package.' Loop Cycle and can now start a new loop</div>';
									
								}						
								///////IF RECEIVING FIRST DONATION/////////////////////////////////////////////////					
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
							
							///////HOWERVER IF ADMIN IS FORCE CONFIRMING AN UNFAIRLY PURGED USER/////////////////////////////////////////////////////////////////////////
							//////HANDLE THE RECEIVER AGRESSIVELY FOR PURGING A GENUINELY PAID USER/////
							////////BY SUSPENSION AND COMPLETE OF LOOP/////////////////////////////////////////
							elseif(isset($_POST["unfairly_purged"])){
																
								    /////////AGRESSIVELY COMPLETE THE RECEIVER'S LOOP CYCLE IN DONATION TABLE ////////////////////
									///////////PDO QUERY////////////////////////////////////	
									$sql = "UPDATE ".$donation_table." SET  LOOP_STATUS = 'COMPLETE', MATCH_STATUS = 'MATCHED-COMPLETE'  WHERE ID = ?   LIMIT 1";

									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($rec_did));
									
									/*********SET RECYCLING DEADLINE******************/
									$recyl_deadline = getRecyclingDeadline();
									$comment = 'YOUR ACCOUNT WAS SUSPENDED FOR INGENUINELY PURGING A PARTICIPANT';
									/////////ALSO AGRESSIVELY COMPLETE THE RECEIVER'S LOOP CYCLE AND ////////
									///////  SUSPEND HIM IN MEMBERS TABLE ////////////////////
									///////////PDO QUERY////////////////////////////////////	
									$sql = "UPDATE members SET  LOOP_STATUS = 'COMPLETE', CURRENT_PACKAGE = 'NONE', FLOW_DIRECTION = 'NONE', SUSPENSION_STATUS = 'YES', COMMENT1=?  WHERE USERNAME = ?   LIMIT 1";

									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($comment,$receiver));
									
									$alert2 = '<div id="green" class="errors blink">CONFIRMATION SUCCESSFUL<br/> The current target receiver has been suspended and his loop cycle completed.</div>';
									
								
								
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
			
	
	/////////////////////////////////END OF FCFM//////////////////////////////////////////////////////////////////////
			
				
			if(isset($_POST["pack_name"])){
				$tname = protect($_POST["pack"]);
			}
			if(isset($_GET["pack"])){
				$tname = protect($_GET["pack"]);
			}
			if(!$tname)
				$tname = 'classic';
			
			$donation_table = 'euro_'.strtolower($tname).'_donations';
			$matching_table = 'euro_'.strtolower($tname).'_matching';
	
			//////DEFINE ARRAY OF PACKAGES SO YOU CAN LOOP THROUGH ALL PACKAGES///////////////////////////////////
			
			$package_arr = getPackagesArray();

			//////////////LOOP THROUGH EACH PACKAGES ////////////////////////////////////
			
			foreach($package_arr as $pack_name){
				if($tname == $pack_name)
					$tab_options .= '<option selected>'.$pack_name.'</option>';
				else
					$tab_options .= '<option>'.$pack_name.'</option>';
				
			}
					
					
	/**********GET SORT ORDER************************************/	
		if(isset($_GET["sort"]))
			$get_sort =	protect(strtolower($_GET["sort"]));
			
		if(isset($_POST["sort"]))
			$get_sort = protect($_POST["sort"]);

		if(!$get_sort)
			$get_sort = "latest";		

		if($get_sort){
			
					
			if($get_sort  == "latest")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a  class='current_tab' >Latest</a>
								| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
								| <a href='?sort=r0&pack=".$tname."' class='links ' >Not Confirmed</a> |
								<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
								<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
								<a href='?sort=pg_awt&pack=".$tname."' class='links ' >Purge Awaiting</a> |
							  </div> ";
							
				
				

			elseif($get_sort  == "old")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest&pack=".$tname."' class='links' >Latest</a>
								| <a  class='current_tab' >Oldest</a>
								| <a href='?sort=r0&pack=".$tname."' class='links' >Not Confirmed</a> |
								<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
								<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
								<a href='?sort=pg_awt&pack=".$tname."' class='links ' >Purge Awaiting</a> |
							</div>";
							
				

			elseif($get_sort  == "r0")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest&pack=".$tname."' class='links ' >Latest</a>
								| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
								| <a  class='current_tab' >Not Confirmed</a> |
								<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
								<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
								<a href='?sort=pg_awt&pack=".$tname."' class='links ' >Purge Awaiting</a> |
							</div>";
					
				

			elseif($get_sort  == "r1")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest&pack=".$tname."' class='links ' >Latest</a>
								| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
								| <a href='?sort=r0&pack=".$tname."' class='links' >Not Confirmed</a> |
								<a class='current_tab ' >Confirmed</a> |
								<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
								<a href='?sort=pg_awt&pack=".$tname."' class='links ' >Purge Awaiting</a> |
							</div>";
			
			elseif($get_sort  == "awt")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest&pack=".$tname."' class='links ' >Latest</a>
								| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
								| <a href='?sort=r0&pack=".$tname."' class='links' >Not Confirmed</a> |
								<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
								<a class='current_tab ' >Awaiting</a> |	
								<a href='?sort=pg_awt&pack=".$tname."' class='links ' >Purge Awaiting</a> |
							</div>";
							
			elseif($get_sort  == "pg_awt")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest&pack=".$tname."' class='links ' >Latest</a>
								| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
								| <a href='?sort=r0&pack=".$tname."' class='links' >Not Confirmed</a> |
								<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
								<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
								<a class='current_tab ' >Purge Awaiting</a> |							
							</div>";
							
				
		}
		else
			$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a  class='current_tab' >Latest</a>
								| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
								| <a href='?sort=r0&pack=".$tname."' class='links ' >Not Confirmed</a> |
								<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
								<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
								<a href='?sort=pg_awt&pack=".$tname."' class='links ' >Purge Awaiting</a> |
							  </div> ";
		
		
		if($get_sort == "latest")
			$order_by = "ORDER BY TIME_OF_PLEDGE DESC";
		
		elseif($get_sort == "old")
			$order_by = "ORDER BY TIME_OF_PLEDGE ASC";
		
		elseif($get_sort == "r0")
			$order_by = " WHERE CONFIRMED IN('PENDING', 'DECLINED')";
		
		elseif($get_sort == "r1")
			$order_by = " WHERE CONFIRMED = 'YES'";
		
		elseif($get_sort == "awt")
			$order_by = " WHERE MATCH_STATUS IN('AWAITING','SEMI-MATCHED')";
		
		elseif($get_sort == "pg_awt")
			$order_by = " WHERE MATCH_STATUS IN( 'AWAITING-AND-PURGED', 'SEMI-MATCHED-PURGED')";
		
	
		if(isset($_POST["trx_num"]) && $_POST["trx_num"]){
			
			$trx_num_srch = protect($_POST["trx_num"]);
			$order_by = " WHERE TRANS_NUMBER = '".$trx_num_srch."'";
					
		}
		///////////PDO QUERY////////////////////////////////////	
								
		$sql = "SELECT * FROM  ".$donation_table." ".$order_by;

		$stmt1 = $pdo_conn_login->prepare($sql);
		$stmt1->execute(array());
		
		if($stmt1->rowCount()){
						
				
			//////////////////////////////////////////////////PAGINATION////////////////////////////////////////////////

			////////////////GET THE PAGE ID///////////////////////////////////////////////////////////////////////////////////////

			$total_records="";$per_page="";$total_page="";

			$total_records = $stmt1->rowCount();

			///////////////////////////////SET THE MAX NUMBER OF RECORDS TO DISPLAY IN EACH PAGE////////////////////////////////////////////////////////////////////////

			$per_page = 10;


			////////////////////GET THE TOTAL PAGES THAT THE ENTIRE RECORD WILL TAKE///////////////////////////////////////////////////////////////////////////////////// 

			$total_page = ceil($total_records/$per_page);

			//////////////GET THE PAGE ID IF THERE IS AN INPUT TO JUMP TO PAGE////////////////////////////

			if(isset($_POST["jump_page"])){
				
				
				if($_POST["page_input"] )
				$page_id = preg_replace("#[^0-9]#", "", $_POST["page_input"]);

				
				else
					$page_id = $total_page;
				
				
			}

			//////////////////////ELSE GET THE PAGE ID PASSED/////////////////////////////////////////////

			if(isset($_GET["page_id"])){
				
					if($_GET["page_id"] )
					$page_id = preg_replace("#[^0-9]#", "", $_GET["page_id"]);
					

			}

				if($page_id == "" || $page_id == 0)
						$page_id = 1;




			////MAKE SURE THE PAGE_ID PASSED DOES NOT EXCEED THE TOTAL PAGES THE ENTIRE RECORDS CAN TAKE//////////////////////////////////////////////////////////////////////////////////////

			if($page_id > $total_page)
				
				$page_id = $total_page;

				$page_id_out = $page_id;

				
			//////////////////CALCULATE THE STARTING FROM THE PAGE ID PASSED///////////////////////////////////////////////////////////////////////////////////////


			$start_rec = ($page_id * $per_page) - $per_page; 


			if($start_rec < 0)
				$start_rec = 0;
			 
			 
			////////////////////GENERATE THE PAGINATION LINKS//////////////////////////////////////////////////////////////////////////////////////////
			 
			 $pagination_links="";$next_page="";$prev_page="";$first_page="";$last_page="";
			 
			 
			 ///////////////////SHOW THE PAGINATION ONLY IF THE TOTAL RECORDS IN DB EXCEEDS A PAGE//////////////////////////////////////////////////////////////////////////////////////
			  
			 if($total_page > 1){
				 
			///////////////////ONLY DISPLAY THE PREV PAGE NAVIGATOR WHEN THERE IS ACTUALLY A PREVIOUS PAGE /////////////////////////////////////////////////////////////////////////////////////
				 
					if($page_id > 1){
						
						
			 ////////////////////DEFINE PREV_PAGE/////////////////////////////////////////////////////////////////////////////////////
			 
			 $prev_page = $page_id - 1;
			 

						
					$prev_page = "<a href='?sort=".$get_sort."&pack=".$tname."&page_id=".$prev_page." '><span><< </span>Prev</a> ";	
						
					
					
					for($i=($page_id - 4); $i < $page_id; $i++){
						
						if($i < 1)
							continue;
						
						$pagination_left .= "<a href='?sort=".$get_sort."&pack=".$tname."&page_id=".$i." '>".$i."</a> ";	
						
						
				}
					
			}


				 
			///////////////////ONLY DISPLAY THE NEXT  PAGE NAVIGATOR WHEN THERE IS ACTUALLY A NEXT PAGE /////////////////////////////////////////////////////////////////////////////////////
				 
					if($page_id != $total_page){
						
						
			////////////////////DEFINE NEXT_PAGE/////////////////////////////////////////////////////////////////////////////////////
			 
			 $next_page = $page_id + 1;
			 
						
					$next_page = "<a href='?sort=".$get_sort."&pack=".$tname."&page_id=".$next_page."' >Next<span> >></span></a> ";	
					
					for($i=$page_id + 1; $i <= ($page_id + 4); $i++ ){
						
						
					$pagination_right .="<a href='?sort=".$get_sort."&pack=".$tname."&page_id=".$i." '>".$i."</a> ";	
				
					if($i == $total_page)
						break;
				
				}

					
			}


			//////////////DEFINE FIRST PAGE////////////////////////////////////////////////////////////////////////////////////////////	 
				 
				 if($page_id > 1){
				 $first_page = 1;
				 
				 $first_page = "<a href='?sort=".$get_sort."&pack=".$tname."&page_id=".$first_page." '>First</a> ";	
				
			}	
			///////DEFINE LAST PAGE///////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				if($page_id != $total_page)	{
				$last_page = $total_page;
				
				 $last_page = "<a href='?sort=".$get_sort."&pack=".$tname."&page_id=".$last_page." '>Last</a> ";	

			}
			//////////////////////GENERATE THE FINAL PAGINATION BEHAVIOR////////////////////////////////////////////////////////////////////////////////////
				 
				 
				 $pagination = "<div class='pagination'>".$first_page.$prev_page.$pagination_left."<span id=current_page>".$page_id."</span> ".$pagination_right.$next_page.$last_page." <form class=jump2page  method=post action='?page_id='><li class=jump2page_wrapper id=jump2page_wrapper ><input type=text name=page_input /><input class=jump2page_button id=jump2page_button type=submit name=jump_page value='Jump to page' /></li><input type='hidden' name='pack' value='".$tname."' /><input type='hidden' name='sort' value='".$get_sort."' /></form><a  id='skippage' title='jump to page' onclick='return false;' href='#' class='skippage links'><img class='pageskip' src='wealth-island-images/icons/skippage.png' alt='icon' /></a></div>";
				 
				 
			 }

			/////////////////////////////////END OF PAGINATION/////////////////////////////////////////////////////////////////	
		
			
			///////////PDO QUERY////////////////////////////////////	
									
			$sql = "SELECT * FROM  ".$donation_table."  ".$order_by."  LIMIT ".$start_rec.",".$per_page;

			$stmt2 = $pdo_conn_login->prepare($sql);
			$stmt2->execute(array());
			
			$sn=1;
			
			while($rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
					
					////////////////IMPORTANT: ALWAYS RESET THESE VARIABLES DURING ITERATION////////////////////////////////////////////
					$did=$d_username=$cfm_btn=$pop=$indicate_receiver=$indicate_purged_payer=$purger_rematch_btn=$append_pop="";
				
					$did = $rows["ID"];
					$d_username = $rows["USERNAME"];
					$target_package = $rows["PACKAGE"];
					$match_status = $rows["MATCH_STATUS"];
					
					////////////GET PAYER'S OR DONATOR'S FROM WHERE THEY WERE MATCHED TO PAY POP AND INFOS AND SHOW THEM ONLY IF THEY EXIST//////////////////////////////////
					///////////PDO QUERY////////////////////////////////////	
											
					$sql = "SELECT PAYER_USERNAME,PAYER_DID,METHOD_OF_PAY,PAYMENT_SLIP_NAME,UPLOADED_PROOF,TIME_OF_PAY FROM  ".$matching_table."  WHERE PAYER_DID = ? AND PAYER_USERNAME = ?   LIMIT 1 ";

					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array($did,$d_username));
					if($stmt->rowCount()){
						
						$pop_row = $stmt->fetch(PDO::FETCH_ASSOC);
							
						if($pop_row["UPLOADED_PROOF"]){
							$pop = 'Method of Pay: '.$pop_row["METHOD_OF_PAY"].' <br/> ';
							$pop .= 'Name on Slip: '.$pop_row["PAYMENT_SLIP_NAME"].' <br/> ';
							$pop .= 'Uploaded File:<br/><img class="pop" alt="pop" src="wealth-island-uploads/proof_of_payments/'.$pop_row["UPLOADED_PROOF"].'"/> <br/> ';
							$pop .= 'Time of Pay: '.dateFormatStyle($pop_row["TIME_OF_PAY"]).' <br/> ';														
								
							//////////////////CHECH IF PURGER REMATCH BUTTON IS NEEDED////////////////////////////////////////////////////
							if($match_status == "AWAITING-AND-PURGED" || $match_status == "SEMI-MATCHED-PURGED"){
									
									/////////// PURGED PAYER'S PAYMENT DETAILS//////////									
								
									$sql = "SELECT PAYER_USERNAME,PAYER_DID,TIME_OF_PAY,METHOD_OF_PAY,PAYMENT_SLIP_NAME,UPLOADED_PROOF FROM  ".$matching_table."  WHERE REC_DID = ? AND REC_USERNAME = ? AND (UPLOADED_PROOF !='' AND  CONFIRMED IN('PENDING','DECLINED')) ORDER BY ID DESC  LIMIT 1 ";
													
									$stmt = $pdo_conn_login->prepare($sql);
									$stmt->execute(array($did,$d_username));
									$purge_row = $stmt->fetch(PDO::FETCH_ASSOC);
									
									////////////GET PURGED PAYER'S TIME OF PAY FOR COMPARISM TO KNOW WHEN TO ///////
									///////////////DISPLAY PURGER REMATCH BUTTON////////////////
									$purged_payer_top = $purge_row["TIME_OF_PAY"];
									$purged_payer_did = $purge_row["PAYER_DID"];
									$purged_payer_username = $purge_row["PAYER_USERNAME"];
									
									/////////////////GET POP OF THE PURGED USER INSTEAD SO ADMINS CAN SEE WHEN REVIEWING PURGES /////////////////////////////////////////////////
									
									$pop = 'Method of Pay: '.$purge_row["METHOD_OF_PAY"].' <br/> ';
									$pop .= 'Name on Slip: '.$purge_row["PAYMENT_SLIP_NAME"].' <br/> ';
									$pop .= 'Uploaded File:<br/><img class="pop" alt="pop" src="wealth-island-uploads/proof_of_payments/'.$purge_row["UPLOADED_PROOF"].'"/> <br/> ';
									$pop .= 'Time of Pay: '.dateFormatStyle($purge_row["TIME_OF_PAY"]).' <br/> ';														
									
									$append_pop = 'PURGED PAYER\'S ';
									
									////////////DETERMINE PURGER REMATCH TYPE///////////////////////////////////
									
									/////REMATCH PURGER TO RECEIVE TWICE IF HE HAS'NT RECEIVED ANY DONATIONS AT ALL/////////////////
									if($match_status == "AWAITING-AND-PURGED")
										$rematch_purger_by = "MATCH-BY-TWO";
									
									/////REMATCH PURGER TO RECEIVE ONCE IF HE HAS RECEIVED ONE DONATION BEFORE/////////////////
									if($match_status == "SEMI-MATCHED-PURGED")
										$rematch_purger_by = "MATCH-BY-ONE";
							
									$indicate_purged_payer = '<tr>
																<th>PURGED PAYER:</th>
																<td class="red">
																	'.$purged_payer_username.'
																	<form method="post" target="_blank" action="members">
																		<input type="hidden" name="sq" value="'.$purged_payer_username.'" />
																		<input class="formButtons" type="submit" name="view_search" value="View Information" />
																	</form>																	
																</td>
															</tr>
															<tr>
																<th>PURGED PAYER\'S DID</th>
																<td class="red">'.$purged_payer_did.'</td>
															</tr>';
									
									/*********GIVE OPTIONS TO ADMINS REMATCH PURGER AWAITING REMATCH *****************************************/									
									//////////////////ENSURE THAT PURGER REMATCH BUTTON ONLY SHOWS UP AFTER 30 HRS(3600*30) TO GIVE///////////
									///////////////////ADMINS TIME TO LOOK INTO THE PURGING////////////////////////////////////////////////
									if(($purged_payer_top + 108000 ) <= time()){																		
									//if((1491135220 + 300) <= time()){																		
										
										$purger_rematch_btn = '
													<input type="button" class="formButtons confirm_paid"  value="REMATCH PURGER" />
															<span></span>
																<div class="modal">																						
																	<div class="modal_content">
																		<div class="modal_header clear">SET PURGER REMATCH<span class="close_modal">&times;</span></div>			
																		<span class="red">ATTENTION: ADMIN, YOU ARE ABOUT TO SET A MEMBER FOR REMATCH, ARE YOU SURE YOU WANT TO EXECUTE THIS ACTION </span>
																		<form method="post" action="donations">
																			<label>AVN<span class="red">*</span></label>
																			<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
																			<label>TARGET DID=<span class="red">'.$did.'</span>,
																				<input autocomplete="off" required placeholder="Enter the target donation id" type="hidden" name="did" value="'.$did.'" class="only_form_textarea_inputs" />
																				MATCH TYPE=<span class="red">'.$rematch_purger_by.'</span>,
																				<input required placeholder="Enter the match algo here" type="hidden" value="'.$rematch_purger_by.'" name="match" class="only_form_textarea_inputs" />
																				PACKAGE=<span class="red">'.$target_package.'</span>
																				<input required placeholder="Enter the target package" type="hidden" name="pack" value="'.$target_package.'" class="only_form_textarea_inputs" />
																			</label>
																			<br/><input type="submit" name="run_purger_rematch" class="formButtons" value="YES" />
																			<input type="button"  class="formButtons close_modal" value="NO" />
																		</form>
																	</div>
																</div>';
									}
									
							}
																									
						}
					}
					
					/*********GIVE OPTIONS TO ADMINS TO CONFIRM UNCONFIRMED DONATORS BOTH PENDING AND DECLINED*****************************************/	
					///////////PDO QUERY////////////////////////////////////	
											
					$sql = "SELECT REC_USERNAME,REC_DID,METHOD_OF_PAY,PAYMENT_SLIP_NAME,UPLOADED_PROOF,TIME_OF_PAY FROM  ".$matching_table."  WHERE PAYER_DID = ? AND PAYER_USERNAME = ? AND (UPLOADED_PROOF !='' AND  CONFIRMED IN('PENDING','DECLINED'))  LIMIT 1 ";

					$stmt3 = $pdo_conn_login->prepare($sql);
					$stmt3->execute(array($did,$d_username));
					if($stmt3->rowCount()){
						
						$cfm_row = $stmt3->fetch(PDO::FETCH_ASSOC);
						$rec_did = $cfm_row["REC_DID"];
						$rec_username = $cfm_row["REC_USERNAME"];
						
						$indicate_receiver = '<tr>
												<th>RECEIVER:</th>
												<td class="green">
													'.$rec_username.'
													<form method="post" target="_blank" action="members">
														<input type="hidden" name="sq" value="'.$rec_username.'" />
														<input class="formButtons" type="submit" name="view_search" value="View Information" />
													</form>
												</td>												
											 </tr>
											 <tr>
												<th>REC DID</th>
												<td class="green">'.$rec_did.'</td>
											 </tr>
											';
						
						//$pop = 'Method of Pay: '.$cfm_row["METHOD_OF_PAY"].' <br/> ';
						//$pop .= 'Name on Slip: '.$cfm_row["PAYMENT_SLIP_NAME"].' <br/> ';
						//$pop .= 'Uploaded File:<br/><img class="pop" alt="pop" src="wealth-island-uploads/proof_of_payments/'.$cfm_row["UPLOADED_PROOF"].'"/> <br/> ';
						//$pop .= 'Time of Pay: '.dateFormatStyle($cfm_row["TIME_OF_PAY"]).' <br/> ';
						
						$cfm_btn = '
									<input type="button" class="formButtons confirm_paid"  value="CONFIRM" />
											<span></span>
											<div class="modal">											
												<div class="modal_content">
													<div class="modal_header clear">DISBURSEMENT RECEIPT CONFIRMATION  <span class="close_modal">&times;</span></div>													
													<span class="red">ADMIN, ARE YOU SURE THIS MEMBER HAS MADE DISBURSEMENT</span>
													<form method="post" action="donations">													
														<label>AVN<span class="red">*</span></label>														
														<input required autocomplete="off" placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />
														<label>TARGET DID=<span class="red">'.$did.' </span></label>														
														<label>
															UNFAIRLY PURGED<span class="red">*</span>
															<input type="checkbox" name="unfairly_purged" value="" />
														</label>
														<input type="hidden" name="dtab" value="'.$donation_table.'" />
														<input type="hidden" name="mtab" value="'.$matching_table.'" />
														<input type="hidden" name="fcfm" value="true" />
														<input type="hidden" name="rec_did" value="'.$rec_did.'" />
														<input type="hidden" name="payer_did" value="'.$did.'" /><br/>
														<input type="submit"  name="final_confirm" class="formButtons"  value="YES AM SURE" />
														<input type="button"  class="formButtons close_modal"  value="NOT YET" />
													</form>																							
												</div>
											</div>';
											
					}
										
					
					
					
						
				
					$datas .= '<th>DONATION '.$sn.'</th>
								<tr>
									<th>D_ID</th>
									<td>'.$did.'</td>
								</tr>
								<tr>
									<th>PACKAGE</th>
									<td>'.$rows["PACKAGE"].'</td>
								</tr>
								<tr>
									<th>INVESTMENT</th>							
									<td>'.formatNumber($rows["AMOUNT_PLEDGED"]).'</td>
								</tr>
								<tr>
									<th>RETURN</th>							
									<td>'.formatNumber($rows["RETURN_AMOUNT"]).'</td>
								</tr>
								<tr>
									<th>USERNAME</th>							
									<td>'.$d_username.'</td>
								</tr>
								<tr>
									<th>D-TIME</th>							
									<td>'.dateFormatStyle($rows["TIME_OF_PLEDGE"]).'</td>
								</tr>
								<tr>
									<th>M-CYCLE</th>							
									<td>'.$rows["MATCH_STATUS"].'</td>
								</tr>
								<tr>
									<th>A-MATCHED</th>							
									<td>'.formatNumber($rows["AMOUNT_MATCHED"]).'</td>
								</tr>
								<tr>
									<th>A-REM</th>							
									<td>'.formatNumber($rows["AMOUNT_REM"]).'</td>
								</tr>
								<tr>
									<th>L_CYCLE</th>							
									<td>'.$rows["LOOP_STATUS"].'</td>
								</tr>
								<tr>
									<th>POD</th>														
									<td>'.$rows["PAID_OR_DECLINED"].'</td>
								</tr>
								<tr>
									<th>CFM</th>							
									<td>'.$rows["CONFIRMED"].'</td>
								</tr>
								<tr>
									<th>CFM_TIME</th>							
									<td>'.dateFormatStyle($rows["CONFIRM_TIME"]).'</td>
								</tr>
								<tr>
									<th>RCVD_COUNTS</th>														
									<td>'.$rows["REC_COUNTER"].'</td>
								</tr>
								<tr>
									<th>TRX NUM</th>							
									<td>'.$rows["TRANS_NUMBER"].'</td>
								</tr>
								<tr>
									<th>'.$append_pop.'POP</th>							
									<td>'.$pop.'</td>
								</tr>
								<tr>
									<th>FCFM</th>
									<td>'.$cfm_btn.'</td>								
								</tr>
								'.$indicate_receiver.'	
								<tr>
									<th>PURGER REMATCH</th>
									<td>'.$purger_rematch_btn.'</td>
								</tr>															
								'.$indicate_purged_payer.'
								<tr>
									<th></th>
									<td></td>
								</tr>
								<tr>
									<th></th>
									<td></td>
								</tr>
								<tr>
									<th></th>
									<td></td>
								</tr>';
								
								$sn++;
			}
			
			if($datas)
				$datas = '	<div class="tab_wrap">
								<table>'.										
								$datas.
							'	</table>
							</div>';
		
		}else{
			$datas = '<div class="blink errors">Sorry no donation was found matching your request</div>';
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
<title>DONATIONS</title>
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

		echo "<a href='donations' title=>Donations</a> "  ;
				
		?>
	</header>
	<?php if(getUserPrivilege($username) == 'ADMIN'){ ?>
	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>
	
		<?php if(isset($not_logged))   echo $not_logged;
				
				
			if($pagination)
			$curr_page = '(Page <span class="cyan">'.$page_id.'</span> of '.$total_page.')';
		
			echo '<h1 class="h_bkg"> <img class="min_img" src="wealth-island-images/icons/strelka_rt.png" />  '.strtoupper($tname).' PACKAGE DONATIONS  <img class="min_img" src="wealth-island-images/icons/strelka_lt.png" />  <br/>'.$curr_page.'</h1>';
			if(isset($alert))   echo $alert;
			if($pagination) echo $pagination;
			if(isset($sort_html)) echo $sort_html;
			echo '<form method="post" action="donations">
					<fieldset>
						<label>Package</label><select name="pack" class="only_form_textarea_inputs">'.$tab_options.'</select>
						<label>Transaction number - <span style="font-size:15px; color:#ff0000">Enter a transaction number to get a particular transaction from this package or leave blank to get all transactions</span></label><input placeholder="Enter a transaction number" type="text" name="trx_num" value="'.$trx_num_srch.'" class="only_form_textarea_inputs" />
						<input type="submit" name="pack_name" class="formButtons" value="SELECT" />
					</fieldset>
				  </form>';
				  
			if(isset($alert2)) echo $alert2;
			if($datas) echo $datas;
			if($pagination) echo $pagination;			
			  
		?>
		
		

	</div>
	<?php }else{ echo '<div class="view_user_wrapper" id="hide_vuwbb"><h2 class="red">Sorry you do not have enough privilege to view this page!!!</h2></div>';}  ?>
	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>-->
	<span id="go_down"></span>

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>