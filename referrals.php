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
$datas = "";$page_id="";$page_id_out="";$start_rec=""; $pagination="";$pagination_right="";$pagination_left="";
$total_page="";$curr_page="";$get_sort="";

$username = $_SESSION["username"];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if($username){
	
	/**********GET SORT ORDER************************************/
		
		if(isset($_POST["sort"]))
			$get_sort = protect($_POST["sort"]);

		if(!$get_sort)
			$get_sort = "latest";
		
		if(isset($_GET["sort"]))		
			$get_sort =	protect(strtolower($_GET["sort"]));		
									

		if($get_sort){			

			if($get_sort  == "latest")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a  class='current_tab' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a href='?sort=r1' class='links ' >Cashed</a> |
								<a href='?sort=r0' class='links ' >Not Cashed</a> |
								<a href='?sort=c1' class='links ' >Confirmed</a> |
								<a href='?sort=c0' class='links ' >Not Confirmed</a> |
							  </div> ";
							
				
				

			elseif($get_sort  == "old")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest' class='links' >Latest</a>
								| <a class='current_tab ' >Oldest</a>
								| <a href='?sort=r1' class='links ' >Cashed</a> |
								<a href='?sort=r0' class='links ' >Not Cashed</a> |
								<a href='?sort=c1' class='links ' >Confirmed</a> |
								<a href='?sort=c0' class='links ' >Not Confirmed</a> |
							  </div> ";				
							
				

			elseif($get_sort  == "r0")
				$sort_html = "<div class='postul'><h3 >SORT BY: </h3>
								| <a href='?sort=latest' class='links' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a href='?sort=r1' class='links ' >Cashed</a> |
								<a class='current_tab' >Not Cashed</a> |
								<a href='?sort=c1' class='links ' >Confirmed</a> |
								<a href='?sort=c0' class='links ' >Not Confirmed</a> |
							  </div> ";	
					
				

			elseif($get_sort  == "r1")
				$sort_html = "<div class='postul'><h3 >SORT BY: </h3>
								| <a href='?sort=latest' class='links' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a class='current_tab' >Cashed</a> |
								<a href='?sort=r0' class='links ' >Not Cashed</a> |
								<a href='?sort=c1' class='links ' >Confirmed</a> |
								<a href='?sort=c0' class='links ' >Not Confirmed</a> |
							  </div> ";	

			
			elseif($get_sort  == "c0")
				$sort_html = "<div class='postul'><h3 >SORT BY: </h3>
								| <a href='?sort=latest' class='links' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a href='?sort=r1' class='links ' >Cashed</a> |
								<a href='?sort=r0' class='links ' >Not Cashed</a> |
								<a href='?sort=c1' class='links ' >Confirmed</a> |
								<a class='current_tab' >Not Confirmed</a> |
							  </div> ";	
			
			
			elseif($get_sort  == "c1")
				$sort_html = "<div class='postul'><h3 >SORT BY: </h3>
								| <a href='?sort=latest' class='links' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a href='?sort=r1' class='links ' >Cashed</a> |
								<a href='?sort=r0' class='links ' >Not Cashed</a> |
								<a class='current_tab' >Confirmed</a> |
								<a href='?sort=c0' class='links ' >Not Confirmed</a> |
							  </div> ";	
							  
							
				
		}
		else			
			if($get_sort  == "latest")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a  class='current_tab' >Latest</a>
								| <a href='?sort='old' class='links ' >Oldest</a>
								| <a href='?sort=r1' class='links ' >Cashed</a> |
								<a href='?sort=r0' class='links ' >Not Cashed</a> |
								<a href='?sort=c1' class='links ' >Confirmed</a> |
								<a href='?sort=c0' class='links ' >Not Confirmed</a> |
							  </div> ";			
		
		if($get_sort == "latest")
			$order_by = "ORDER BY TIME DESC";
		
		elseif($get_sort == "old")
			$order_by = "ORDER BY TIME ASC";
		
		elseif($get_sort == "r0")
			$order_by = " AND REMIT_STATUS = 'PENDING'";
		
		elseif($get_sort == "r1")
			$order_by = " AND REMIT_STATUS = 'CASHED'";
		
		elseif($get_sort == "c0")
			$order_by = " AND CONFIRMATION = 'PENDING'";
		
		elseif($get_sort == "c1")
			$order_by = " AND CONFIRMATION = 'CONFIRMED'";
		
		
	
	///////////PDO QUERY////////////////////////////////////	
							
	$sql = "SELECT ID FROM referrals WHERE REFERRAL = ? ".$order_by;

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
		 

					
				$prev_page = "<a href='?sort=".$get_sort."&page_id=".$prev_page." '><span><< </span>Prev</a> ";	
					
				
				
				for($i=($page_id - 4); $i < $page_id; $i++){
					
					if($i < 1)
						continue;
					
					$pagination_left .= "<a href='?sort=".$get_sort."&page_id=".$i." '>".$i."</a> ";	
					
					
			}
				
		}


			 
		///////////////////ONLY DISPLAY THE NEXT  PAGE NAVIGATOR WHEN THERE IS ACTUALLY A NEXT PAGE /////////////////////////////////////////////////////////////////////////////////////
			 
				if($page_id != $total_page){
					
					
		////////////////////DEFINE NEXT_PAGE/////////////////////////////////////////////////////////////////////////////////////
		 
		 $next_page = $page_id + 1;
		 
					
				$next_page = "<a href='?sort=".$get_sort."&page_id=".$next_page."' >Next<span> >></span></a> ";	
				
				for($i=$page_id + 1; $i <= ($page_id + 4); $i++ ){
					
					
				$pagination_right .="<a href='?sort=".$get_sort."&page_id=".$i." '>".$i."</a> ";	
			
				if($i == $total_page)
					break;
			
			}

				
		}


		//////////////DEFINE FIRST PAGE////////////////////////////////////////////////////////////////////////////////////////////	 
			 
			 if($page_id > 1){
			 $first_page = 1;
			 
			 $first_page = "<a href='?sort=".$get_sort."&page_id=".$first_page." '>First</a> ";	
			
		}	
		///////DEFINE LAST PAGE///////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			if($page_id != $total_page)	{
			$last_page = $total_page;
			
			 $last_page = "<a href='?sort=".$get_sort."&page_id=".$last_page." '>Last</a> ";	

		}
		//////////////////////GENERATE THE FINAL PAGINATION BEHAVIOR////////////////////////////////////////////////////////////////////////////////////
			 
			 
			 $pagination = "<div class='pagination'>".$first_page.$prev_page.$pagination_left."<span id=current_page>".$page_id."</span> ".$pagination_right.$next_page.$last_page." <form class=jump2page  method=post action='?page_id='><li class=jump2page_wrapper id=jump2page_wrapper ><input type=text name=page_input /><input class=jump2page_button id=jump2page_button type=submit name=jump_page value='Jump to page' /></li><input type='hidden' name='sort' value='".$get_sort."' /></form><a  id='skippage' title='jump to page' onclick='return false;' href='#' class='skippage links'><img class='pageskip' src='wealth-island-images/icons/skippage.png' alt='icon' /></a></div>";
			 
			 
		 }

		/////////////////////////////////END OF PAGINATION/////////////////////////////////////////////////////////////////	

			
		///////////PDO QUERY////////////////////////////////////	
								
		$sql = "SELECT * FROM referrals WHERE REFERRAL = ?  ".$order_by." LIMIT ".$start_rec.",".$per_page;

		$stmt2 = $pdo_conn_login->prepare($sql);
		$stmt2->execute(array($username));
		
		$sn=1;
		
		while($rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
			
			$datas .= 	'<tr>
							<td>
								'.$sn.'
							</td>
							<td>
								'.$rows["REFERRED"].'
							</td>
							<td>
								'.dateFormatStyle($rows["TIME"]).'
							</td>
							<td>
								'.$rows["INCENTIVE"].' NgN
							</td>	
							<td>
								'.$rows["CONFIRMATION"].'
							</td>
							<td>
								'.$rows["REMIT_STATUS"].'
							</td>														
						</tr>';
						//<td>
							//'.$rows["INCENTIVE"].'
						//</td>
							
			
				$sn++;
			
		}
		
			$datas = '<div class="tab_wrap"><table><th>S/N</th><th>REFERRED MEMBER</th><th>TIME REFERRED</th><th>REWARD</th><th>STATUS</th><th>REMIT STATUS</th>'.$datas.'</table></div>';//<th>COMMISSION</th>
					
	}
	else{
		$datas = '<div class="blink errors">Sorry no referral was found matching your request</div>';
	}
	
	
	
	/*******GET AMOUNT AVAILABLE FOR WITHDRAWAL, AMOUNT CONFIRMED, AMOUNT NOT CONFIRMED************************/	
		
	///////////PDO QUERY////////////////////////////////////	
							
	$sql = "SELECT ID FROM referrals WHERE REFERRAL = ? ";

	$stmtx = $pdo_conn_login->prepare($sql);
	$stmtx->execute(array($username));
	
	if($stmtx->rowCount()){
		
								
		$cashout_lim = 40;	$per_rwd = 500;
		
		/*********AVAILABLE FOR WITHDRAWAL*******************/
		///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT * FROM referrals WHERE REFERRAL = ? AND CONFIRMATION = 'CONFIRMED' AND REMIT_STATUS = 'PENDING' LIMIT ".$cashout_lim;

		$stmt3 = $pdo_conn_login->prepare($sql);
		$stmt3->execute(array($username));
		if($stmt3->rowCount() == $cashout_lim){
			
			$avail = '<h2>AVAILABLE FOR CASHOUT: ₦'.formatNumber(($stmt3->rowCount() * $per_rwd)).' NgN</h2>
						
							<input type="button" value="CASHOUT" class="formButtons start_btn" />					
							<span></span>
							<div class="modal">																						
								<div class="modal_content">
									<div class="modal_header clear">REFERRAL REWARD CASHOUT<span class="close_modal">&times;</span></div>						
									<div class="errors">ATTENTION!!!<br/> YOU ARE ABOUT TO CASHOUT YOUR REFERRAL REWARDS <br/>ARE YOU SURE YOU STILL WANT TO CASHOUT </div>
									<form action="referrals" method="post">
										<label>AVN<span class="red">*</span></label>
										<input autocomplete="off" required placeholder="Enter Your AVN" type="text" name="avn" value="" class="only_form_textarea_inputs" />																				
										<br/><input type="submit" name="cashout" id="clsc_btn" class="formButtons" value="YES" />
										<input type="button"  class="formButtons close_modal" id="clsc_btn" value="NO" />
									</form>
								</div>
							</div>';
							
		}else{
			$avail = '<h2><span class="green">AVAILABLE FOR CASHOUT</span>: ₦'.formatNumber(0).' NgN</h2>';
		}
		
		
		/*********AMOUNT CONFIRMED*******************/
		
		///////////PDO QUERY////////////////////////////////////	
								
		$sql = "SELECT * FROM referrals WHERE REFERRAL = ? AND CONFIRMATION = 'CONFIRMED'";

		$stmt4 = $pdo_conn_login->prepare($sql);
		$stmt4->execute(array($username));
		if($stmt4->rowCount()){
			
			$amt_confirmed = '<h2><span class="green">CONFIRMED REWARDS</span>: ₦'.formatNumber(($stmt4->rowCount()* $per_rwd)).' NgN</h2>';
		}else{
			$amt_confirmed = '<h2><span class="green">CONFIRMED REWARDS</span>: ₦'.formatNumber(0).' NgN</h2>';
		}
		
		
		/*********AMOUNT NOT CONFIRMED*******************/
		
		///////////PDO QUERY////////////////////////////////////	
								
		$sql = "SELECT * FROM referrals WHERE REFERRAL = ? AND CONFIRMATION = 'PENDING'";

		$stmt5 = $pdo_conn_login->prepare($sql);
		$stmt5->execute(array($username));
		if($stmt5->rowCount()){
			
			$amt_not_confirmed = '<h2><span class="red">UNCONFIRMED REWARDS</span>: ₦'.formatNumber(($stmt5->rowCount()* $per_rwd)).' NgN</h2>';
		}else{
			$amt_not_confirmed = '<h2><span class="red">UNCONFIRMED REWARDS</span>: ₦'.formatNumber(0).' NgN</h2>';
		}
		
		
		$get_avilables = '<div class="hiw type_a">'.$avail.$amt_confirmed.$amt_not_confirmed.'</div>';
		
	}
	
	
	/**************ON REQUEST TO CASHOUT*******************************/
	
	if(isset($_POST["cashout"])){
		
		$avn = $_POST["avn"];
		
	/////////////////MAKE SURE THAT USERS DONT HAVE IMCOMPLETE CYCLES BEFORE THEY START A NEW ONE//////////////

	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT CURRENT_PACKAGE FROM members WHERE USERNAME = ? AND CURRENT_PACKAGE !='NONE'  LIMIT 1";

		$stmt6 = $pdo_conn_login->prepare($sql);
		$stmt6->execute(array($username));
		$chk_row = $stmt6->fetch(PDO::FETCH_ASSOC);
		$active_package = $stmt6->rowCount();
		if($active_package)
			$curr_package = $chk_row["CURRENT_PACKAGE"];
		
		if(!$active_package){
			/**********VERIFY AVN********************************************************/
			if(verifyAVN($username,$avn)){
						
				/****CALL THE CASHOUT HANDLER******************************/
				
				referralCashout();
				
				header("location:dash-board?rrc=1");
				exit();
								
				$alert = '<div class="errors">YOUR REQUEST TO CASHOUT YOUR REFERRAL REWARDS HAS BEEN DISPATCHED 
						SUCCESSFULLY. PLEASE CHECK YOUR <a href="dash-board" class="links">DASHBOARD</a> AND WAIT TO 
						BE MERGED TO RECEIVE PAYMENT </div>';
			}
			else{
				
				$alert = '<div class="errors blink">SORRY, AVN VERIFICATION FAILED!!!</div>';
			}
		}else{
				
				$alert = '<div class="errors">SORRY, YOU CANNOT REQUEST REFERRAL CASHOUT WHEN YOU HAVE AN ACTIVE PACKAGE<br/>PLEASE TRY AGAIN WHEN YOUR CURRENT CYCLE IS COMPLETE</div>';
			}
		
			
		
		
	}
	

}
else{
$not_logged="";

//$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

header('location:login');
exit();

}




?>


<!DOCTYPE HTML>
<html>
<head>
<title>REFERRALS</title>
<?php require_once("include-html-headers.php")   ?>
<script></script>

<style>
</style>
</head>

<body>
<div class="wrapper">

	<?php if(isset($_SESSION["username"])) require_once('euromenunav.php') ?>

	<span id="go_up"></span>
			
	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		$page_self = getReferringPage("qstr url");

			echo "<a href='".$page_self."'>Referrals </a> "  ;
			
			
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>	
				
		<?php 
			if(isset($not_logged))   echo $not_logged ; 
			
			if($pagination)
				$curr_page = '(Page <span class=cyan>'.$page_id.'</span> of '.$total_page.')';
			
			echo '<h1  class="h_bkg2">REFERRAL SUMMARIES FOR <span class="red">'.strtoupper($username).'</span><br/>'.$curr_page.'</h1>';
			echo 'Please see <a class="links" href="referral-system">here</a> for more information and guides.';
			if(isset($alert)) echo $alert;
			if(isset($get_avilables)) echo $get_avilables;
			if($pagination) echo $pagination;
			if(isset($sort_html)) echo $sort_html;
			if($datas) echo $datas;
			if($pagination) echo $pagination;			
			
			
						
		?>
		<?php if($username){ ?>
			<h4 class="h_bkg">Your Referral Link is: <span class=""><?php echo $getdomain.'/register?rise='.$username; ?></span><br/><span class="lgreen">Please copy it and share on social medias and amongst friends</span><h4>
		<?php } ?>
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>
