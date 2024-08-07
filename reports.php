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

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sort_order="";$datas = "";$page_id="";$page_id_out="";$start_rec=""; $pagination="";$pagination_right="";$pagination_left="";
$total_page="";$curr_page="";



$username = $_SESSION["username"];

if(getUserPrivilege($username) == 'ADMIN' || getUserPrivilege($username) == 'MODERATOR'){

	if($username){	
				
				/******MARK/UNMARK REPORT STATUS AS TREATED/PENDING****************************************************/
				if(isset($_POST["mark_pending"])){
					
					$rid = protect($_POST["rid"]);
					
					///////////PDO QUERY////////////////////////////////////	
											
					$sql = "UPDATE reports SET STATUS = 'PENDING' WHERE ID = ? LIMIT 1";

					$stmt = $pdo_conn_login->prepare($sql);
					if($stmt->execute(array($rid)))
						$alert = '<span  id="green" class="errors blink">STATUS UPDATED SUCCESSFULLY</span>';
					
					
					
				}
				if(isset($_POST["mark_treated"])){
					
					$rid = protect($_POST["rid"]);
					$lb_time = time();
					
					///////////PDO QUERY////////////////////////////////////	
											
					$sql = "UPDATE reports SET STATUS = 'TREATED', LT_TIME = ?, LAST_TREATED_BY = ? WHERE ID = ? LIMIT 1";

					$stmt = $pdo_conn_login->prepare($sql);
					if($stmt->execute(array($lb_time,$username,$rid)))
						$alert = '<span id="green" class="errors blink">STATUS UPDATED SUCCESSFULLY</span>';
			
					
					
					
				}
				
				
	/**********GET SORT ORDER************************************/	
			
		if(!isset($_GET["sort"]))
			$get_sort = "latest";

		if(isset($_GET["sort"])){
			
			$get_sort =	protect(strtolower($_GET["sort"]));

			if($get_sort  == "latest")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a  class='current_tab' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a href='?sort=r0' class='links ' >Not Pending</a> |
								<a href='?sort=r1' class='links ' >Pending</a> |
							  </div> ";
							
				
				

			if($get_sort  == "old")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest' class='links' >Latest</a>
								| <a  class='current_tab' >Oldest</a>
								| <a href='?sort=r0' class='links' >Not Pending</a> |
								<a href='?sort=r1' class='links ' >Pending</a> |
							</div>";
							
				

			if($get_sort  == "r0")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest' class='links ' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a  class='current_tab' >Not Pending</a> |
								<a href='?sort=r1' class='links ' >Pending</a> |
							</div>";
					
				

			if($get_sort  == "r1")
				$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a href='?sort=latest' class='links ' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a href='?sort=r0' class='links' >Not Pending</a> |
								<a class='current_tab ' >Pending</a> |
							</div>";
							
				
		}
		else
			$sort_html = "<div class='postul'><h3>SORT BY: </h3>
								| <a  class='current_tab' >Latest</a>
								| <a href='?sort=old' class='links ' >Oldest</a>
								| <a href='?sort=r0' class='links ' >Not Pending</a> |
								<a href='?sort=r1' class='links ' >Pending</a> |
							  </div> ";
				
							
		
		if($get_sort == "latest")
			$order_by = "ORDER BY TIME DESC";
		
		if($get_sort == "old")
			$order_by = "ORDER BY TIME ASC";
		
		if($get_sort == "r0")
			$order_by = " WHERE STATUS = 'TREATED'";
		
		if($get_sort == "r1")
			$order_by = " WHERE STATUS = 'PENDING'";
		
					
		
		///////////PDO QUERY////////////////////////////////////	
								
		$sql = "SELECT * FROM reports ".$order_by;

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
				 
				 
				 $pagination = "<div class='pagination'>".$first_page.$prev_page.$pagination_left."<span id=current_page>".$page_id."</span> ".$pagination_right.$next_page.$last_page." <form class=jump2page  method=post action='?page_id='><li class=jump2page_wrapper id=jump2page_wrapper ><input type=text name=page_input /><input class=jump2page_button id=jump2page_button type=submit name=jump_page value='Jump to page' /></li></form><a  id='skippage' title='jump to page' onclick='return false;' href='#' class='skippage links'><img class='pageskip' src='wealth-island-images/icons/skippage.png' alt='icon' /></a></div>";
				 
				 
			 }

			/////////////////////////////////END OF PAGINATION/////////////////////////////////////////////////////////////////	
		
			
			///////////PDO QUERY////////////////////////////////////	
									
			$sql = "SELECT * FROM reports  ".$order_by."  LIMIT ".$start_rec.",".$per_page;

			$stmt2 = $pdo_conn_login->prepare($sql);
			$stmt2->execute(array());
			
			while($rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
				
		
				$edit="";	$stat="";			
					$edit = '<form method="post" action="reports"><input type="hidden" name="rid" value="'.$rows["ID"].'" /><input type="submit"  class="formButtons" name="mark_pending" value="Mark as Pending" /></form>';
				
					
				if($rows["LAST_TREATED_BY"])
						$edit .= '<div class="clear"><a class="reply_pm">Last Treated By <span class="blue">'.$rows["LAST_TREATED_BY"].'</span> '.dateFormatStyle($rows["LT_TIME"]).' <form method="post" target="_blank" action="send-pm"><input type="hidden" name="receiver" value="'.$rows["LAST_TREATED_BY"].'" /><input type="submit"  name="m2m_pm" class="formButtons" id="min_buttons"  value="PM '.$rows["LAST_TREATED_BY"].'" /></form></a></div>';
				
				if($rows["STATUS"] == "PENDING")
					$stat = '<b class="red">PENDING</b>';
				else if($rows["STATUS"] == "TREATED")
					$stat = '<b class="green">TREATED</b>';
			
				$datas .= '<div id="innerInbox" class="clear"><div id="messageWrapper" class="messageWrapper clear"><header id="mssgsender"><b>REPORT FILED BY : <span class="blue">'.$rows["REPORTING_USERNAME"].'</span><span> ' .dateFormatStyle($rows["TIME"]).' </span> AGAINST <span class="red">'.$rows["REPORTED_USERNAME"].'</span>  <a class="reply_pm">STATUS('.$stat.')</a></header><hr/><div  class="mssgcontents" id="mssgcontents">'.$rows["CONTENT"].'<form method="post" action="reports"><input type="hidden" name="rid" value="'.$rows["ID"].'" /><input type="submit" class="formButtons" name="mark_treated" value="Mark As Treated" /></form>'.$edit.'</div></div></div>';
				
			}
		
		}else{
			$datas = '<div class="blink errors">Sorry no report was found matching your request</div>';
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
<title>REPORTS</title>
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

		echo "<a href='reports' title=>Reports</a> "  ;
				
		?>
	</header>
	<?php if(getUserPrivilege($username) == 'ADMIN' || getUserPrivilege($username) == 'MODERATOR'){ ?>
	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper">

		<?php echo getMidPageScroll(); ?>
	
		<?php if(isset($not_logged))   echo $not_logged;
				
				
			if($pagination)
			$curr_page = '(Page <span class="cyan">'.$page_id.'</span> of '.$total_page.')';
		
			echo '<h1 class="h_bkg2"><img class="min_img" src="wealth-island-images/icons/strelka_rt.png" /> REPORTS <img class="min_img" src="wealth-island-images/icons/strelka_lt.png" /><br/>'.$curr_page.'</h1>';
			if(isset($alert))   echo $alert;
			if($pagination) echo $pagination;
			if(isset($sort_html)) echo $sort_html;
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



