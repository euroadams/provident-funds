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
$datas = "";$page_id="";$page_id_out="";$start_rec=""; $pagination="";$pagination_right="";$pagination_left="";$total_page="";$curr_page="";

$username = $_SESSION["username"];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////PDO QUERY////////////////////////////////////	
						
$sql = "SELECT * FROM letters_of_happiness";

$stmt1 = $pdo_conn_login->prepare($sql);
$stmt1->execute();

if($stmt1->rowCount()){
				
		
	//////////////////////////////////////////////////PAGINATION////////////////////////////////////////////////

	////////////////GET THE PAGE ID///////////////////////////////////////////////////////////////////////////////////////

	$total_records="";$per_page="";$total_page="";

	$total_records = $stmt1->rowCount();

	///////////////////////////////SET THE MAX NUMBER OF RECORDS TO DISPLAY IN EACH PAGE////////////////////////////////////////////////////////////////////////

	$per_page = 30;


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
							
	$sql = "SELECT * FROM letters_of_happiness ORDER BY TIME DESC  LIMIT ".$start_rec.",".$per_page;

	$stmt2 = $pdo_conn_login->prepare($sql);
	$stmt2->execute();
	
	
	while($row = $stmt2->fetch(PDO::FETCH_ASSOC)){

		$datas .= '
				<div class="clear"><span class="">'.getDP($row["SENDER"],"NOLINK").'</span><span class="loh_header">'.$row["FULL_NAME"].'<br/><span class="loh_loc">'.$row["LOCATION"].'</span></span></div>
				<div class="loh_content">'.$row["CONTENT"].'</div><div class="clear"><span class="loh_footer">'.dateFormatStyle($row["TIME"]).'</span></div><hr/>';
	
	}
	
	$datas = substr($datas, 0, -5);
	
}
else{
	$datas = '<div class="blink errors">Sorry there are no testimonials yet</div>';
}






?>


<!DOCTYPE HTML>
<html>
<head>
<title>TESTIMONIALS</title>
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

			echo "<a href='".$page_self."'>Testimonials </a> "  ;
		
			
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>	
				
		<?php 
			if(isset($not_logged))   echo $not_logged ; 
			
			if($pagination)
				$curr_page = '(Page <span class=cyan>'.$page_id.'</span> of '.$total_page.')';
			
			echo '<h1  class="blue"><img class="min_img" src="wealth-island-images/icons/strelka_rt.png" /> See What People are saying about us <img class="min_img" src="wealth-island-images/icons/strelka_lt.png" /><br/>'.$curr_page.'</h1>';
			
			if($pagination) echo $pagination;
			if($datas) 
				echo '<div class="loh"><h2 class="cyan">TESTIMONIALS</h2>'.$datas.'</div>';
			if($pagination) echo $pagination;			
			
		?>
		
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>