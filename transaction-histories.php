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
$datas = "";$page_id="";$page_id_out="";$start_rec=""; $pagination="";$pagination_right="";$pagination_left="";$total_page="";$curr_page="";

$username = $_SESSION["username"];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if($username){
			
			
	///////////PDO QUERY////////////////////////////////////	
							
	$sql = "SELECT * FROM transactions WHERE USERNAME = ? ";

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
		 

					
				$prev_page = "<a href='?page_id=".$prev_page." '><span><< </span>Prev</a> ";	
					
				
				
				for($i=($page_id - 4); $i < $page_id; $i++){
					
					if($i < 1)
						continue;
					
					$pagination_left .= "<a href='?page_id=".$i." '>".$i."</a> ";	
					
					
			}
				
		}


			 
		///////////////////ONLY DISPLAY THE NEXT  PAGE NAVIGATOR WHEN THERE IS ACTUALLY A NEXT PAGE /////////////////////////////////////////////////////////////////////////////////////
			 
				if($page_id != $total_page){
					
					
		////////////////////DEFINE NEXT_PAGE/////////////////////////////////////////////////////////////////////////////////////
		 
		 $next_page = $page_id + 1;
		 
					
				$next_page = "<a href='?page_id=".$next_page."' >Next<span> >></span></a> ";	
				
				for($i=$page_id + 1; $i <= ($page_id + 4); $i++ ){
					
					
				$pagination_right .="<a href='?page_id=".$i." '>".$i."</a> ";	
			
				if($i == $total_page)
					break;
			
			}

				
		}


		//////////////DEFINE FIRST PAGE////////////////////////////////////////////////////////////////////////////////////////////	 
			 
			 if($page_id > 1){
			 $first_page = 1;
			 
			 $first_page = "<a href='?page_id=".$first_page." '>First</a> ";	
			
		}	
		///////DEFINE LAST PAGE///////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			if($page_id != $total_page)	{
			$last_page = $total_page;
			
			 $last_page = "<a href='?page_id=".$last_page." '>Last</a> ";	

		}
		//////////////////////GENERATE THE FINAL PAGINATION BEHAVIOR////////////////////////////////////////////////////////////////////////////////////
			 
			 
			 $pagination = "<div class='pagination'>".$first_page.$prev_page.$pagination_left."<span id=current_page>".$page_id."</span> ".$pagination_right.$next_page.$last_page." <form class=jump2page  method=post action='?page_id='><li class=jump2page_wrapper id=jump2page_wrapper ><input type=text name=page_input /><input class=jump2page_button id=jump2page_button type=submit name=jump_page value='Jump to page' /></li></form><a  id='skippage' title='jump to page' onclick='return false;' href='#' class='skippage links'><img class='pageskip' src='wealth-island-images/icons/skippage.png' alt='icon' /></a></div>";
			 
			 
		 }

		/////////////////////////////////END OF PAGINATION/////////////////////////////////////////////////////////////////	

			
		///////////PDO QUERY////////////////////////////////////	
								
		$sql = "SELECT * FROM transactions WHERE USERNAME = ? ORDER BY TRANS_TIME DESC  LIMIT ".$start_rec.",".$per_page;

		$stmt2 = $pdo_conn_login->prepare($sql);
		$stmt2->execute(array($username));
		
		$sn=1; $don1_time="";$don2_time="";
		
		while($rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
			
			$don1_time = $rows["DONATION1_TIME"];
			$don2_time = $rows["DONATION2_TIME"];
			
			if($don1_time)
				$don1_time = dateFormatStyle($don1_time);
			if($don2_time)
				$don2_time = dateFormatStyle($don2_time);
			
			$datas .= 	'<tr>
							<td>
								'.$sn.'
							</td>
							<td>
								'.$rows["PACKAGE"].'
							</td>
							<td>
								'.formatNumber($rows["AMOUNT"]).' NGN
							</td>
							<td>
								'.$rows["TRANS_NUMBER"].'
							</td>
							<td>
								'.$rows["DESCRIPTION"].'
							</td>
							<td>
								'.dateFormatStyle($rows["TRANS_TIME"]).'
							</td>
							<td>
								'.$rows["STATUS"].'
							</td>
							<td>
								'.$rows["DONATION1"].'
							</td>
							<td>
								'.$don1_time.'
							</td>
							<td>
								'.$rows["DONATION2"].'
							</td>
							<td>
								'.$don2_time.'
							</td>
							
						</tr>';
			
				$sn++;
			
		}
		
		$datas = '<div class="tab_wrap"><table><th>S/N</th><th>PACKAGE NAME</th><th>AMOUNT</th><th>TRANSACTION NUMBER</th><th>DESCRIPTION</th><th>TRANSACTION TIME</th><th>STATUS</th><th>GH1</th><th>GH1_TIME</th><th>GH2</th><th>GH2_TIME</th>'.$datas.'</table></div>';
	}
	else{
		$datas = '<div class="blink errors">Sorry you have not made any transactions yet</div>';
	}
	

}
else{
$not_logged="";

$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

}




?>


<!DOCTYPE HTML>
<html>
<head>
<title>TRANSACTIONS</title>
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

			echo "<a href='".$page_self."'>Transactions </a> "  ;
		
			
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<?php echo getMidPageScroll(); ?>	

		<?php 
			if(isset($not_logged))   echo $not_logged ; 
			
			if($pagination)
				$curr_page = '(Page <span class=cyan>'.$page_id.'</span> of '.$total_page.')';
			
			echo '<h1 class="h_bkg">TRANSACTION SUMMARIES FOR <span class="blue">'.strtoupper($username).'</span><br/>'.$curr_page.'</h1>';
			
			if($pagination) echo $pagination;
			if($datas) echo $datas;
			if($pagination) echo $pagination;
				
		?>
		<h3 class="errors">
			NOTE:<br/>STATUS: PENDING - means your pledge has not been redeemed
			<br/>STATUS: PAID - means your pledge has been redeemed 
			<br/>STATUS: SEMI-SUCCESSFUL - means you have received 50% of your returns. 
			<br/>STATUS: SUCCESSFUL - means you have received 100% of your returns. 
			
		</h2>
		
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>
