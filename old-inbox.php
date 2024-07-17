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


if($_SESSION["username"]){

	$data="";$pagination="";

	date_default_timezone_set("Africa/Lagos");

	$username=$_SESSION['username'];
	$prevtimes=time()-43200;




	//////***********************************
	////////////////////DECIDE OLD MESSAGES TO BE MESSAGES FROM ONE WEEK OLDER//////////////////////////////

	$today = time();


	$sql = "UPDATE privatemessage SET  OLD_INBOX = INBOX WHERE (USERNAME=? AND INBOX != ''  AND INBOX_STATUS = 'read' AND (TIME + 604800) <=?) ";

	$stmt = $pdo_conn_login->prepare($sql);

	$stmt->execute(array($username, $today));

	$sql = "UPDATE privatemessage SET INBOX = '' WHERE (USERNAME=? AND INBOX != ''  AND INBOX_STATUS = 'read' AND (TIME + 604800) <=? )";

	$stmt = $pdo_conn_login->prepare($sql);

	$stmt->execute(array($username, $today));

	//////***********************************

	/////////////CHECK IF USER HAS OLD MESSAGES/////////////////////////////////////////////////////////////////

	$sql = "SELECT * FROM privatemessage WHERE USERNAME=? AND OLD_INBOX !='' ORDER BY TIME DESC";

	$stmt1 = $pdo_conn_login->prepare($sql);

	$stmt1->execute(array($username));

	$message4username = $stmt1->rowCount();


	////////////////////////////RESET ALL CHECKS NOT EXECUTED///////////////////////////////////


	$sql = "UPDATE privatemessage SET  SELECTION_STATUS='' WHERE USERNAME=? AND SELECTION_STATUS !=''";

	$stmt2 = $pdo_conn_login->prepare($sql);

	$stmt2->execute(array($username));

	if($message4username){
			
				
		//////////////////////////////////////////////////PAGINATION////////////////////////////////////////////////

		$page_id="";$page_id_out="";$start_rec=""; $pagination="";$pagination_left="";$pagination_right="";

		////////////////GET THE PAGE ID///////////////////////////////////////////////////////////////////////////////////////

		$total_records="";$per_page="";$total_page="";

		$total_records = $message4username;

		///////////////////////////////SET THE MAX NUMBER OF RECORDS TO DISPLAY IN EACH PAGE////////////////////////////////////////////////////////////////////////

		$per_page = 10;


		////////////////////GET THE TOTAL PAGES THAT THE ENTIRE RECORD WILL TAKE///////////////////////////////////////////////////////////////////////////////////// 

		$total_page = ceil($total_records/$per_page);

		//////////////////////////////////////////////



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
			 
			 
			 $pagination = $first_page.$prev_page.$pagination_left."<span id=current_page>".$page_id."</span> ".$pagination_right.$next_page.$last_page." <form class=jump2page  method=post action='?page_id='><li class=jump2page_wrapper id=jump2page_wrapper ><input type=text name=page_input /><input class=jump2page_button id=jump2page_button type=submit name=jump_page value='Jump to page' /></li></form><a  id='skippage' title='jump to page' onclick='return false;' href='#' class='skippage links'><img class='pageskip' src='wealth-island-images/icons/skippage.png' alt='icon' /></a>";
			 
			 
		 }

		/////////////////////////////////END OF PAGINATION/////////////////////////////////////////////////////////////////	


		$sql = "SELECT * FROM privatemessage WHERE USERNAME=? AND OLD_INBOX!='' ORDER BY TIME DESC LIMIT ".$start_rec.",".$per_page;

		$stmt3 = $pdo_conn_login->prepare($sql);

		$stmt3->execute(array($username));


			$atleastonein="";

			while($row=$stmt3->fetch(PDO::FETCH_ASSOC)){
				
				$data .= pmHandler($row, "old_inbox");
				
			}
				
				
				$deleteoldmessages="";

				$deleteoldmessages="(<a onclick='return false;' href='deleteoldmessages' class='deleteoldmessages links' >delete  old message histories</a>)
								
									<div  hidden  class='delete_old_inbox_hist old_inbox_hist'><div class=dropping_arrow_inbox></div><div class='red account_cancel_wrapper  cancel_alert'><b> WARNING!!!<hr/>". strtoupper($_SESSION["username"]) ."<br/><br/> 
									you are about to delete all your old message histories 
									<br/><br/>please click OK to proceed or CANCEL to terminate the action<br/><br/>NOTE: you will no longer be able to access them once deleted<br/>
									<input type=button class='formButtons confirm_old_inbox_del' value=OK /> 
									<input class='formButtons confirm_old_inbox_del'  type=button value=CANCEL /></b></div></div>";
				
				$deleteselected="";
				$deleteselected="(<a class='links' href='delete-selected-old-pm'>delete selected</a>)";
				
		
	}



	else{
		
	$err= "<span>sorry you have no old messages</span>"	;
		
		
		
		
	}


	$inbox_link=" (<a class='links' href='inbox'>View your Inbox </a>)";


}


else{

$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

}


?>
<!DOCTYPE HTML>

<html>
<head>
<title>Old Inbox </title>
<?php require_once("include-html-headers.php")   ?>

<style>

</style>

</head>
<body>
<div class="wrapper">

	<?php require_once('euromenunav.php')   ?>

	<header id="go_up" class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>My Older Messages</a> "  ;
			
	?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<?php  if(isset($not_logged))  echo "<div class=view_user_wrapper>".$not_logged."</div>"  ?>

	<?php  if($_SESSION["username"])  {  ?>

	<div class="view_user_wrapper" id="hide_vuwbb">
	<?php echo getMidPageScroll(); ?>

	<h1 class="h_bkg">OLDER MESSAGES</h1>

	<p id="deletedselection"></p>

	<?php if(isset($deleteselected)) echo $deleteselected ?>
	<?php if(isset($inbox_link)) echo $inbox_link ?>

	<?php if(isset($deleteoldmessages)) echo $deleteoldmessages ?>


	<h1><?php if ($pagination != "") echo "(Page <span class=cyan>".$page_id."</span> of ".$total_page.")"; ?></h1>
	<div class="pagination"><?php if(isset($pagination))   echo $pagination  ?></div>

	<span id="checked_num"></span>

	<div id="innerInbox" class="clear"><?php if(isset($data)) echo $data;  if(isset($err)) echo '<div class="errors blink">'.$err.'</div>'; ?></div>

	<div class="pagination"><?php if(isset($pagination))   echo $pagination  ?></div>

	<hr/>


	<?php if(isset($deleteselected)) echo $deleteselected ?>
	<?php if(isset($inbox_link)) echo $inbox_link ?>
	<?php if(isset($deleteoldmessages)) echo $deleteoldmessages ?>

	<hr/>

	</div>
	<?php } ?>
	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->
	<?php require_once('eurofooter.php')   ?>
</div>
</body>
</html>