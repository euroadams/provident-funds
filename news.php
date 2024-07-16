<?php

session_start();
require_once("forumdb_conn.php");
require_once("phpfunctions.php");

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

$news="";$page_id="";$page_id_out="";$start_rec=""; $pagination="";$pagination_right="";$pagination_left="";$total_page="";$curr_page="";
///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();

setPageTimeZone();


$username = $_SESSION["username"];

///////////PDO QUERY////////////////////////////////////	

$sql = "SELECT * FROM news ORDER BY TIME DESC ";

$stmt1 = $pdo_conn_login->query($sql);
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

		$sql = "SELECT * FROM news ORDER BY TIME DESC LIMIT ".$start_rec.",".$per_page;

		$stmt2 = $pdo_conn_login->query($sql);
	
	
	while($row = $stmt2->fetch(PDO::FETCH_ASSOC)){
			
			$edit="";
			
			if(getUserPrivilege($username) == "ADMIN" || getUserPrivilege($username) == "MODERATOR")
				$edit = '<form method="post" target="_blank" action="write-news"><input type="hidden" name="eid" value="'.$row["ID"].'" /><input id="min_buttons" type="submit" name="pass_news_edit" value="EDIT" /></form>';
		
		
			$news .= '<div class="news">
								<div class="news_header accordion_1_trig" title="click to show or hide content">'.$row["HEADER"].'</div>
								<div class="accordion_1">
									<div id="news_content" class="news_content">'.decodeBBC($row["CONTENT"]).'<div class="clear"><span class="news_tstamp">'.dateFormatStyle($row["TIME"]).$edit.'</span></div></div>								
									<div class="news_footer">'.$row["FOOTER"].'</div>
								</div>
							</div>';
		
	}

}


?>


<!DOCTYPE HTML>
<html>
<head>
<title>NEWS</title>
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

		echo "<a href='".$page_self."'>News</a> "  ;
		
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">
		<h1 class="h_bkg">NEWS</h1>
		<?php echo getMidPageScroll(); ?>	

		<?php 
		
			if ($pagination) echo $pagination;
			if ($news) echo '<div class="news_wrapper">'.$news.'</div>';
			if ($pagination) echo $pagination;
		
		
		?>
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>
