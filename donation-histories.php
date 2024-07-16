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
$total_page="";$curr_page="";$tab_options = "";$tname="";$get_sort="";



$username = $_SESSION["username"];

if($username){	

		if(isset($_POST["pack_name"])){
			$tname = protect($_POST["pack"]);
		}
		if(isset($_GET["pack"])){
			$tname = protect($_GET["pack"]);
		}
		if(!$tname)
			$tname = 'classic';
		
///////DETERMINE THE CSS CLASS FOR THE PACKAGE//////////////////////////////////////////////////////
		switch(strtoupper($tname)){
			
			case "STANDARD":{$inc_amt = 5000; $pack_css = 'std_pack'; $pack_btn_css = 'std_btn'; break;}
			case "CLASSIC":{$inc_amt = 10000; $pack_css = 'clsc_pack'; $pack_btn_css = 'clsc_btn'; break;}
			case "PREMIUM":{$inc_amt = 20000; $pack_css = 'prm_pack'; $pack_btn_css = 'prm_btn'; break;}
			case "ELITE":{$inc_amt = 50000; $pack_css = 'elt_pack'; $pack_btn_css = 'elt_btn'; break;}
			case "LORD":{$inc_amt = 100000; $pack_css = 'lrd_pack'; $pack_btn_css = 'lrd_btn'; break;}
			case "MASTER":{$inc_amt = 200000; $pack_css = 'mst_pack'; $pack_btn_css = 'mst_btn'; break;}
			case "ROYAL":{$inc_amt = 500000; $pack_css = 'roy_pack'; $pack_btn_css = 'roy_btn'; break;}
			case "ULTIMATE":{$inc_amt = 1000000; $pack_css = 'ult_pack'; $pack_btn_css = 'ult_btn'; break;}
			
		}
		
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
						  </div> ";
						
			
			

		elseif($get_sort  == "old")
			$sort_html = "<div class='postul'><h3>SORT BY: </h3>
							| <a href='?sort=latest&pack=".$tname."' class='links' >Latest</a>
							| <a  class='current_tab' >Oldest</a>
							| <a href='?sort=r0&pack=".$tname."' class='links' >Not Confirmed</a> |
							<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
							<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
						</div>";
						
			

		elseif($get_sort  == "r0")
			$sort_html = "<div class='postul'><h3>SORT BY: </h3>
							| <a href='?sort=latest&pack=".$tname."' class='links ' >Latest</a>
							| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
							| <a  class='current_tab' >Not Confirmed</a> |
							<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
							<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
						</div>";
				
			

		elseif($get_sort  == "r1")
			$sort_html = "<div class='postul'><h3>SORT BY: </h3>
							| <a href='?sort=latest&pack=".$tname."' class='links ' >Latest</a>
							| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
							| <a href='?sort=r0&pack=".$tname."' class='links' >Not Confirmed</a> |
							<a class='current_tab ' >Confirmed</a> |
							<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
						</div>";
		
		elseif($get_sort  == "awt")
			$sort_html = "<div class='postul'><h3>SORT BY: </h3>
							| <a href='?sort=latest&pack=".$tname."' class='links ' >Latest</a>
							| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
							| <a href='?sort=r0&pack=".$tname."' class='links' >Not Confirmed</a> |
							<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
							<a class='current_tab ' >Awaiting</a> |							
						</div>";
						
			
	}
	else
		$sort_html = "<div class='postul'><h3>SORT BY: </h3>
							| <a  class='current_tab' >Latest</a>
							| <a href='?sort=old&pack=".$tname."' class='links ' >Oldest</a>
							| <a href='?sort=r0&pack=".$tname."' class='links ' >Not Confirmed</a> |
							<a href='?sort=r1&pack=".$tname."' class='links ' >Confirmed</a> |
							<a href='?sort=awt&pack=".$tname."' class='links ' >Awaiting</a> |
						  </div> ";
	
	
	if($get_sort == "latest")
		$order_by = "ORDER BY TIME_OF_PLEDGE DESC";
	
	elseif($get_sort == "old")
		$order_by = "ORDER BY TIME_OF_PLEDGE ASC";
	
	elseif($get_sort == "r0")
		$order_by = " AND CONFIRMED IN('PENDING','DECLINED')";
	
	elseif($get_sort == "r1")
		$order_by = " AND CONFIRMED = 'YES'";
	
	elseif($get_sort == "awt")
		$order_by = " AND MATCH_STATUS IN('AWAITING','SEMI-MATCHED', 'AWAITING-AND-PURGED', 'SEMI-MATCHED-PURGED')";
	
				
	
	///////////PDO QUERY////////////////////////////////////	
							
	$sql = "SELECT * FROM  ".$donation_table." WHERE USERNAME = ? ".$order_by;

	$stmt1 = $pdo_conn_login->prepare($sql);
	$stmt1->execute(array($username));
	
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
								
		$sql = "SELECT * FROM  ".$donation_table." WHERE USERNAME = ?  ".$order_by."  LIMIT ".$start_rec.",".$per_page;

		$stmt2 = $pdo_conn_login->prepare($sql);
		$stmt2->execute(array($username));
		
		$sn=1;
		
		while($rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
				
				$did="";$d_username="";$all_ph="";$all_gh="";$pop="";$trnx_num="";$amount_pledged=$order_date="";
			
				$did = $rows["ID"];
				$d_username = $rows["USERNAME"];
				$trnx_num = $rows["TRANS_NUMBER"];
				$order_date = dateFormatStyle($rows["TIME_OF_PLEDGE"]);
				$amount_pledged = $rows["AMOUNT_PLEDGED"];
				
				///////////PDO QUERY////////////////////////////////////	
										
				$sql = "SELECT FLOW_DIRECTION FROM  members  WHERE USERNAME = ?  LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($username));
				
				$fdr_row = $stmt->fetch(PDO::FETCH_ASSOC);
				$fdr = $fdr_row["FLOW_DIRECTION"];
				
				
				/********DO FOR PROVIDE HELP(PH) OR DONATING******************************************/
				/*********GET ALL MATCHES CORRESPONDING TO THE DID*****************************************/	
				///////////PDO QUERY////////////////////////////////////	
										
				$sql = "SELECT * FROM  ".$matching_table."  WHERE PAYER_DID = ? AND PAYER_USERNAME = ?  ";

				$stmt3 = $pdo_conn_login->prepare($sql);
				$stmt3->execute(array($did, $d_username));
				if($stmt3->rowCount()){														
					
					while($ph_row = $stmt3->fetch(PDO::FETCH_ASSOC)){
						
						$rec_did = $ph_row["REC_DID"];
						$rec_username = $ph_row["REC_USERNAME"];
						$paid_or_decl = $ph_row["PAID_OR_DECLINED"];
						$payment_time = $ph_row["TIME_OF_PAY"];
						$payment_method = $ph_row["METHOD_OF_PAY"];
						$payment_name = $ph_row["PAYMENT_SLIP_NAME"];
						$uploaded_pop = $ph_row["UPLOADED_PROOF"];
						$cfm = $ph_row["CONFIRMED"];
												
						///////////GET THE RECEIVING USER's DETAILS //////////////
						/////////PDO QUERY////////////////////////////////////	
			
						$sql = "SELECT * FROM members  WHERE USERNAME = ? LIMIT 1";

						$stmt4 = $pdo_conn_login->prepare($sql);
						$stmt4->execute(array($rec_username));
						if($stmt4->rowCount()){
							$rec_detail = $stmt4->fetch(PDO::FETCH_ASSOC);
							$rec_email = $rec_detail["EMAIL"];
							$rec_phone = $rec_detail["MOBILE_PHONE"];
							$rec_alt_phone = $rec_detail["ALT_MOBILE_PHONE"];
							$rec_acc_name = $rec_detail["ACCOUNT_NAME"];
							$rec_bnk_name = $rec_detail["BANK_NAME"];
							$rec_acc_num = $rec_detail["ACCOUNT_NUMBER"];
							$rec_fn = $rec_detail["FULL_NAME"];
							$rec_avatar = getDP($rec_username,"NOLINK");
							$rec_gender = $rec_detail["GENDER"];
						}	
						
												
						$pop="";//IMPORTANT/////
						///////////GET POP ONLY IF THE USER HAS PAID////////////////////////////////////////////
						if($paid_or_decl == "PAID"){
							if($cfm == "YES")
								$pop = '<h2>TRANSACTION STATUS: <span id="green">SUCCESSFUL</span><h2>';
							else
								$pop = '<h2>TRANSACTION STATUS: <span id="yellow">AWAITING CONFIRMATIION</span><h2> ';
							
							$pop .= '<hr/><h3><span>AMOUNT DISBURSED:</span> ₦'.formatNumber($amount_pledged).'<h3>';
							$pop .= '<h3><span>YOU PAID:</span> '.dateFormatStyle($payment_time).'<h3><hr/>';
							$pop .= '<div class="pop_details">
										<h1>PROOF OF PAYMENT SENT</h1>
										<h4><span>PAYMENT METHOD: </span>'.$payment_method.'</h4>							
										<h4><span>NAME USED: </span>'.$payment_name.'</h4>
										<h4><span>UPLOADED PROOF:<br/></span><img class="pop" alt="pop" src="wealth-island-uploads/proof_of_payments/'.$uploaded_pop.'"/></h4>
									</div>';							
						}
						else{
							if($paid_or_decl == "DECLINED")
								$pop = '<h2>TRANSACTION STATUS: <span class="red">DECLINED</span><h2> ';
							else
								$pop = '<h2>TRANSACTION STATUS: <span class="red">PENDING</span><h2> ';
						}
						
											
						$all_ph .= '<div class="dh_accordion_wrap">
										<h1 class="h_bkg2 accordion_2_trig">ORDER - '.$trnx_num.' (Provide Help) <img alt="icon" class="min_img" src="wealth-island-images/icons/strelka_dwn.png" /></h1>
										<div class="packages accordion_2 '.$pack_css.'">
											<h2 class="cyan">Order Date: '.$order_date.'</h2>
											<h1>PLEASE DISBURSE TO:</h1>
											<h5 class="clear">'.$rec_avatar.'</h5>
											<h3><span>USERNAME:</span> '.$rec_username.'</h3>
											<h3><span>FULL NAME:</span> '.$rec_fn.'</h3>											
											<h3><span>EMAIL:</span> <a class="links" href="mailto:'.$rec_email.'">'.$rec_email.'</a></h3>
											<h3><span>PHONE 1:</span> '.$rec_phone.'</h3>
											<h3><span>PHONE 2:</span> '.$rec_alt_phone.'</h3>	
											<form method="post" target="_blank" action="send-pm">											
												<input type="hidden" name="receiver" value="'.$rec_username.'" />
												<input type="submit"  name="m2m_pm" class="formButtons" id="'.$pack_btn_css.'" value="SEND PM" />											
											</form>	
											<h2 class="h_bkg">Bank Details</h2>
											<h3><span>BANK NAME:</span> '.$rec_bnk_name.'</h3>
											<h3><span>ACCOUNT NUMBER:</span> '.$rec_acc_num.'</h3>
											<h3><span>ACCOUNT NAME:</span> '.$rec_acc_name.'</h3><hr/>
											<h3><span>OUTGOING DONATION:</span> ₦'.formatNumber($amount_pledged).'</h3><hr/>
											'.$pop.'
										</div>
									</div>';
						
					}
																
					
				}//////IF DONATING BUT HAVENT BEEN MATCHED///////////////////////////
				else{
					if($fdr == "OUT")
						$all_ph = '<div class="dh_accordion_wrap">
										<h1 class="h_bkg2 accordion_2_trig">NEW ORDER - '.$trnx_num.' (Provide Help) <img alt="icon" class="min_img" src="wealth-island-images/icons/strelka_dwn.png" /></h1>
										<div class="accordion_2">
											<h2 class="cyan">Order Date: '.$order_date.'</h2>
											<div class="packages '.$pack_css.'"><h1>AWAITING MATCH</h1><h3><span>OUTGOING DONATION:</span> ₦'.formatNumber($amount_pledged).'  </h3></div>
										</div>
									</div>';
	
				}
				
				
				
				/********DO FOR GET HELP(GH) OR RECEIVING DONATION******************************************/
				/*********GET ALL MATCHES CORRESPONDING TO THE DID*****************************************/	
				///////////PDO QUERY////////////////////////////////////	
										
				$sql = "SELECT * FROM  ".$matching_table."  WHERE REC_DID = ? AND REC_USERNAME = ? ORDER BY ID DESC  ";

				$stmt3 = $pdo_conn_login->prepare($sql);
				$stmt3->execute(array($did, $d_username));
				if($stmt3->rowCount()){
																	
					while($gh_row = $stmt3->fetch(PDO::FETCH_ASSOC)){
						
						$payer_did = $gh_row["PAYER_DID"];
						$payer_username = $gh_row["PAYER_USERNAME"];
						$paid_or_decl = $gh_row["PAID_OR_DECLINED"];
						$payment_time = $gh_row["TIME_OF_PAY"];
						$payment_method = $gh_row["METHOD_OF_PAY"];
						$payment_name = $gh_row["PAYMENT_SLIP_NAME"];
						$uploaded_pop = $gh_row["UPLOADED_PROOF"];
						$cfm = $gh_row["CONFIRMED"];	
												
						///////////GET THE PAYER'S OR DONATOR'S DETAILS //////////////
						/////////PDO QUERY////////////////////////////////////	
			
						$sql = "SELECT * FROM members  WHERE USERNAME = ? LIMIT 1";

						$stmt4 = $pdo_conn_login->prepare($sql);
						$stmt4->execute(array($payer_username));
						if($stmt4->rowCount()){
							$payer_detail = $stmt4->fetch(PDO::FETCH_ASSOC);
							$payer_email = $payer_detail["EMAIL"];
							$payer_phone = $payer_detail["MOBILE_PHONE"];
							$payer_alt_phone = $payer_detail["ALT_MOBILE_PHONE"];
							$payer_acc_name = $payer_detail["ACCOUNT_NAME"];
							$payer_bnk_name = $payer_detail["BANK_NAME"];
							$payer_acc_num = $payer_detail["ACCOUNT_NUMBER"];
							$payer_fn = $payer_detail["FULL_NAME"];
							$payer_avatar = getDP($payer_username,"NOLINK");
							$payer_gender = $payer_detail["GENDER"];
						}	
						
						
												
						$pop="";//IMPORTANT/////
						///////////GET POP ONLY IF THE USER HAS PAID////////////////////////////////////////////
						if($paid_or_decl == "PAID"){
							if($cfm == "YES")
								$pop = '<hr/><h2>TRANSACTION STATUS: <span id="green">SUCCESSFUL</span><h2> ';
							else
								$pop = '<hr/><h2>TRANSACTION STATUS: <span id="yellow">AWAITING CONFIRMATIION</span><h2> ';
							$pop .= '<hr/><h3><span>AMOUNT RECEIVED:</span> ₦'.formatNumber($amount_pledged).' <h3> ';
							$pop .= '<h3><span>PAYMENT WAS MADE:</span> '.dateFormatStyle($payment_time).' <h3><hr/>';
							$pop .= '<div class="pop_details">
										<h1>PROOF OF PAYMENT SENT</h1>
										<h4><span>PAYMENT METHOD: </span>'.$payment_method.'</h4>							
										<h4><span>NAME USED: </span>'.$payment_name.'</h4>
										<h4><span>UPLOADED PROOF:<br/></span><img class="pop" alt="pop" src="wealth-island-uploads/proof_of_payments/'.$uploaded_pop.'"/></h4>
									</div>';							
						}
						else{
							if($paid_or_decl == "DECLINED")
								$pop = '<hr/><h2>TRANSACTION STATUS: <span class="red">DECLINED</span><h2> ';
							else
								$pop = '<hr/><h2>TRANSACTION STATUS: <span class="red">PENDING</span><h2> ';
						}
						
											
						$all_gh .= '<div class="dh_accordion_wrap">
										<h1 class="h_bkg2 accordion_2_trig" title="Click to show or hide transaction details">ORDER - '.$trnx_num.' (Get Help) <img alt="icon" class="min_img" src="wealth-island-images/icons/strelka_dwn.png" /></h1>
										<div class="packages accordion_2 '.$pack_css.'">
											<h2 class="cyan">Order Date: '.$order_date.'</h2>
											<h1>AWAITING PAYMENT FROM:</h1>
											<h5 class="clear">'.$payer_avatar.'</h5>
											<h3><span>USERNAME:</span> '.$payer_username.'</h3>
											<h3><span>FULL NAME:</span> '.$payer_fn.'</h3>
											<h3><span>INCOMING DONATION:</span> ₦'.formatNumber($amount_pledged).'</h3>
											<h3><span>EMAIL:</span> <a class="links" href="mailto:'.$payer_email.'">'.$payer_email.'</a></h3>
											<h3><span>PHONE 1:</span> '.$payer_phone.'</h3>
											<h3><span>PHONE 2:</span> '.$payer_alt_phone.'</h3>	
											<form method="post" target="_blank" action="send-pm">											
												<input type="hidden" name="receiver" value="'.$payer_username.'" />
												<input type="submit"  name="m2m_pm" class="formButtons" id="'.$pack_btn_css.'" value="SEND PM" />											
											</form>	
											'.$pop.'
										</div>
									</div>';
						
					}
																
					
				}
				else{/////////IF GETTING HELP BUT HAVING BEEN MATCHED////////////////////////////////////////
					
					if($fdr == "IN")
						$all_gh =  '<div class="dh_accordion_wrap">
										<h1 class="h_bkg2 accordion_2_trig" title="Click to show or hide transaction details" >NEW ORDER - '.$trnx_num.' (Get Help) <img alt="icon" class="min_img" src="wealth-island-images/icons/strelka_dwn.png" /> </h1>
										<div class="accordion_2">
											<h2 class="cyan">Order Date: '.$order_date.'</h2>
											<div class="packages '.$pack_css.'"><h1>AWAITING MATCH</h1><h3><span>INCOMING DONATION:</span> ₦'.formatNumber($amount_pledged).'  </h3></div>
											<div class="packages '.$pack_css.'"><h1>AWAITING MATCH</h1><h3><span>INCOMING DONATION:</span> ₦'.formatNumber($amount_pledged).'  </h3></div>				
										</div>
									</div>';
				}
					
					
			
				$datas .= $all_gh.$all_ph;
							
							
		}
		
	
	}else{
		$datas = '<div class="blink errors">Sorry no donation was found matching your request</div>';
	}
		
}
else{

$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

}

?>


<!DOCTYPE HTML>
<html>
<head>
<title>DONATION HISTORIES</title>
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

		echo "<a href='donation-histories' title=>Donation Histories</a> "  ;
				
		?>
	</header>
	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>

		<?php 

			if(isset($not_logged))   echo $not_logged;
				
				
			if($pagination)
			$curr_page = '(Page <span class="cyan">'.$page_id.'</span> of '.$total_page.')';

			echo '<h1 class="h_bkg"> <img class="min_img" src="wealth-island-images/icons/strelka_rt.png" />  '.strtoupper($tname).' DONATION HISTORIES  <img class="min_img" src="wealth-island-images/icons/strelka_lt.png" />  <br/>'.$curr_page.'</h1>';
			echo '<div class="">';
				if(isset($alert))   echo $alert;
				if($pagination) echo $pagination;
				if(isset($sort_html)) echo $sort_html;
				echo '<form method="post" action="donation-histories">
						<fieldset>
							<label>Package</label><select name="pack" class="only_form_textarea_inputs">'.$tab_options.'</select>
							<input type="submit" name="pack_name" class="formButtons" value="SELECT" />
						</fieldset>
					  </form>';
				if($datas) echo '<div class="dh_wrapper">'.$datas.'</div>';
				if($pagination) echo $pagination;			
				  
			?>
		</div>
	</div>
	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>-->
	<span id="go_down"></span>

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>