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
$rep_cnt="";

$username = $_SESSION["username"];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if($username){		

	
	$page_self = getReferringPage("qstr url");
	//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////
	
	if(isset($_COOKIE["form_gate_response"])){
		
		$alert = $_COOKIE["form_gate_response"];
		
			
	/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

			setcookie("form_gate_response", "", (time() -  1800));

	}	


///////////IF INLINE REPLY IS SET/////////////////////////////////////////////
	if(isset($_POST["inline_reply"])){
		
		$message = protect($_POST["inline_reply_cnt"]);
		$messagesubject = 'RE: '.protect($_POST["subject"]);
		$hid = protect($_POST["hid"]);
		$ticket_num = protect($_POST["ticket"]);
		$sender = $username;
		$timesent = time();
		
		if($hid && $ticket_num && $message && $messagesubject){
								
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "INSERT INTO support_replies (SUBJECT,REPLY_CONTENT,SENDER,TIME,TICKET_NO,HID) VALUES(?,?,?,?,?,?)";

			$stmt = $pdo_conn_login->prepare($sql);

			if($stmt->execute(array($messagesubject,$message,$sender,$timesent,$ticket_num,$hid)))				
				$alert = '<span id="green" class="errors">Your message has been posted successfully</span>';	
			
			
			////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
			echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($alert)."')</script>";
				
			
		}
		else{
			$alert = '<span class="errors">Please enter your message in the text field</span>';
		}
		
	}

	
	///////////PDO QUERY////////////////////////////////////	
							
	$sql = "SELECT * FROM help WHERE SENDER = ? ";

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
								
		$sql = "SELECT * FROM help WHERE SENDER = ? ORDER BY TIME DESC  LIMIT ".$start_rec.",".$per_page;

		$stmt2 = $pdo_conn_login->prepare($sql);
		$stmt2->execute(array($username));
		
		while($rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
			
			$hid = $rows["ID"];
			$ticket_num = $rows["TICKET_NO"];
			$rep_cnt="";
			///////////PDO QUERY////////////////////////////////////	
									
			$sql = "SELECT * FROM support_replies WHERE HID = ? AND TICKET_NO = ? ORDER BY TIME DESC ";

			$stmt3 = $pdo_conn_login->prepare($sql);
			$stmt3->execute(array($hid,$ticket_num));
			
			$close_rmk = '<br/><br/><div class="spt_rmk">Best regards<br/><a class="links" href="'.$getdomain.'">'.$domain_name.'</a> <a class="links" href="contact-support">Support</a> Teams</div>';
			
			while($row = $stmt3->fetch(PDO::FETCH_ASSOC)){
					if($row["SENDER"] == $username)			
						$rep_cnt .= '<hr/><span class="green">'.$row["SUBJECT"].'</span><div class="clear">'.$row["REPLY_CONTENT"].'<a id="green" class="reply_pm">SENT BY YOU '.dateFormatStyle($row["TIME"]).'</a></div>';				
					else
						$rep_cnt .= '<hr/><span class="red">'.$row["SUBJECT"].'</span><div class="clear">'.$row["REPLY_CONTENT"].$close_rmk.'<a class="reply_pm">SENT '.dateFormatStyle($row["TIME"]).'</a></div>';				
				
			}
			
			if($rep_cnt)
				$rep_cnt = '<hr/><h2 class="lgreen h_bkg">REPLIES</h2>'.substr($rep_cnt, 5 );
			
			$inrep_form = '<hr/><form method="post" action="support-tickets"><textarea name="inline_reply_cnt" class="only_form_textarea_inputs"></textarea><input type="hidden" name="ticket" value="'.$ticket_num.'" /><input type="hidden" name="hid" value="'.$hid.'" /><input type="hidden" name="subject" value="'.$rows["SUBJECT"].'" /><input class="formButtons" type="submit" name="inline_reply" value="Post" /></form>';
						
			
			$datas .= '<div id="innerInbox" class="clear"><div id="messageWrapper" class="messageWrapper clear"><header id="mssgsender" ><b>TICKET NUMBER:'.$ticket_num.' </b> <span class="blue">(created '.dateFormatStyle($rows["TIME"]).')</span>  <a class="reply_pm">REPLIES('.$rows["TOTAL_REPLIES"].')</a></header><hr/><p id="messagesubject" class="messagesubject"><span id="subjectshow">Subject:</span> <span class="yellow">'.$rows["SUBJECT"].'</span></p><div  class="mssgcontents" id="mssgcontents">'.$rows["CONTENT"].$rep_cnt.$inrep_form.'</div></div></div>';
			
		}
		
	}
	else{
		$datas = '<div class="blink errors">Sorry you have not created any support tickets yet</div>';
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
<title>SUPPORT TICKETS</title>
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

			echo "<a href='".$page_self."'>Support Tickets </a> "  ;
		
			
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>	
				
		<?php 
			if(isset($not_logged))   echo $not_logged ; 
			
			if($pagination)
				$curr_page = '(Page <span class=cyan>'.$page_id.'</span> of '.$total_page.')';
			
			echo '<h1 class="h_bkg">SUPPORT TICKET FOR <span class="lgreen">'.strtoupper($username).'</span><br/>'.$curr_page.'</h1>';
			
			if(isset($alert)) echo $alert;
			if($pagination) echo $pagination;
			if($datas) echo $datas;
			if($pagination) echo $pagination;			
			
		?>
	
		
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>
