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

$not_logged="";$credit_amt="";$other_credit_amt="";$action_srch="";$allowed="";$output="";$output_all="";
$total_records="";

if($_SESSION["username"]){

	$username = $_SESSION["username"];
	
/////////////////GET THE USER PRIVILEGE///////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
			$privilege = getUserPrivilege($username);	
			
			if($privilege != "ADMIN" && $privilege != "MODERATOR" )
					$no_view_privilege = "<h1 class=cyan> <a href='user-profile' class=links>".$username."</a> Sorry You do not have enough Privilege to view this Page</h1>";
	

////////////IF A SEARCH IS SET//////////////////////////////////////////////////////////////////////////////////////////////////////


if(isset($_POST["search"]) || isset($_POST["view_search"])){
	
	
	$uid = protect($_POST["sq"]);
	
	
	if($uid){
		
		$allowed=1;
		
		if(strtolower($uid) == "seer" || strtolower($uid) == "euroadams"){
			
			if(strtolower($username) == "seer"  || strtolower($username) == "euroadams")
					$allowed = 1;
				else
					$allowed = 0;
		
		}
			
	if($allowed){
		
//////////FETCH THE USER DATAS FROM DB/////////////////////
			
///////////PDO QUERY////////////////////////////////////
			
				$sql = "SELECT * FROM members WHERE USERNAME = ? LIMIT 1";

				$stmt4 = $pdo_conn_login->prepare($sql);
				$stmt4->execute(array( $uid));
		
	if($stmt4->rowCount()){
		
		$user = $stmt4->fetch(PDO::FETCH_ASSOC);
			
			
		$output = 	"<caption><h1 class='h_bkg'>".strtoupper(trim($uid))."'s DATAS</h1></caption>
					<table class='search_user_table'>
		
								<tr>
									<th>USER ID</th>
									
									<td>".$user["ID"]."</td>
								
								</tr>
		
		
								<tr>
									<th>USERNAME</th>
									
									<td>".$user["USERNAME"]."</td>
								
								</tr>
		
		
								<tr>
									<th>EMAIL</th>
									
									<td>".$user["EMAIL"]."</td>
								
								</tr>
		
		
								<tr>
									<th>FULL NAME</th>
									
									<td>".ucfirst($user["FULL_NAME"])."</td>
								
								</tr>
		
		
								<tr>
									<th>GENDER</th>
									
									<td>".$user["GENDER"]."</td>
								
								</tr>
		
		
								<tr>
									<th>REGISTERED ON:</th>
									
									<td>".dateFormatStyle($user["TIME"])."</td>
								
								</tr>								
								
								<tr>
									<th>AVATAR:</th>
									
									<td>".getDP($user["USERNAME"],"NOLINK")."</td>
								
								</tr>
									
								<tr>
									<th>BIRTHDAY</th>
									
									<td>".$user["DOB"]."</td>
								
								</tr>
								
								<tr>
									<th>ACCOUNT STATUS</th>
									
									<td>".$user["ACCOUNT_STATUS"]."</td>
								
								</tr>
								<tr>
									<th>SUSPENSION STATUS</th>
									
									<td>".$user["SUSPENSION_STATUS"]."</td>
								
								</tr>
								<tr>
									<th>PRIVILEGE</th>
									
									<td>".$user["USER_PRIVILEGE"]."</td>
								
								</tr>
								<tr>
									<th>COUNTRY</th>
									
									<td>".$user["COUNTRY"]."</td>
								
								</tr>
								<tr>
									<th>STATE</th>
									
									<td>".$user["STATE"]."</td>
								
								</tr>
								<tr>
									<th>ADDRESS</th>
									
									<td>".$user["ADDRESS"]."</td>
								
								</tr>
								<tr>
									<th>MARITAL</th>
									
									<td>".$user["MARITAL_STATUS"]."</td>
								
								</tr>
								<tr>
									<th>PHONE 1</th>
									
									<td>".$user["MOBILE_PHONE"]."</td>
								
								</tr>
								<tr>
									<th>PHONE 2</th>
									
									<td>".$user["ALT_MOBILE_PHONE"]."</td>
								
								</tr>
								<tr>
									<th>BANK</th>
									
									<td>".$user["BANK_NAME"]."</td>
								
								</tr>
								<tr>
									<th>ACCOUNT HOLDER</th>
									
									<td>".$user["ACCOUNT_NAME"]."</td>
								
								</tr>
								<tr>
									<th>ACCOUNT NUMBER</th>
									
									<td>".$user["ACCOUNT_NUMBER"]."</td>
								
								</tr>
								<tr>
									<th>CYCLE</th>
									
									<td>".$user["LOOP_STATUS"]."</td>
								
								</tr>
								<tr>
									<th>PACKAGE</th>
									
									<td>".$user["CURRENT_PACKAGE"]."</td>
								
								</tr>
								<tr>
									<th>FDIRECT</th>
									
									<td>".$user["FLOW_DIRECTION"]."</td>
								
								</tr>
								<tr>
									<th>PURGES</th>
									
									<td>".$user["TOTAL_PURGE"]."</td>
								
								</tr>
								<tr>
									<th>DECLINATIONS</th>
									
									<td>".$user["TOTAL_DECL"]."</td>
								
								</tr>
								<tr>
									<th>RECY DEADLINE</th>									
									<td>".dateFormatStyle($user["RECYCLING_DEADLINE"])."</td>								
								</tr>
		
		
					</table>";
			
			
	}

		else
			
			$alert_user = "<span class='red'>Sorry the user <span class='lgreen'>".$uid."</span> was not found. Please verify the username you entered and try again.</span>";
	
	}
	
	else
		$alert_user = "<span class='red'>Sorry you do not have enough privilege to view <span class='lgreen'>".$uid."'s</span> Datas</span>";
	
	
}
	
	else
		$alert_user = "<span class='red'>Please enter the username of the member you want to search !</span>";
	
	
	
	
}



//////////////////////////TABLE OF ALL MEMBERS////////////////////////////////////////////////////////////////////////////////////////////////

if($privilege == "ADMIN"  || $privilege == "MODERATOR"){
		
		if(isset($_GET["srt"]))		
			$action_srch = strtolower(trim($_GET["srt"]));
		
		if(isset($_POST["srt"])){
			
			$action_srch = strtolower(trim($_POST["srt"]));
			$_GET["srt"] = $action_srch;
		}
			
		if($action_srch == "latest")
			$order_by = " ORDER BY TIME DESC";
		
		elseif($action_srch == "old")
			$order_by = " ORDER BY TIME ASC ";
		
		elseif($action_srch == "alphabets")
			$order_by = " ORDER BY USERNAME";
		
		elseif($action_srch == "s1")
			$order_by = " WHERE SUSPENSION_STATUS = 'YES' ";
		
		elseif($action_srch == "a0")
			$order_by = " WHERE ACCOUNT_STATUS != 'ACTIVATED'";
		
		elseif($action_srch == "rc")
			$order_by = " WHERE USERNAME != '' ORDER BY TIME ASC ";
		
		else
			$order_by = " ORDER BY USERNAME";
		
		
			
///////////PDO QUERY////////////////////////////////////
			
				$sql =  "SELECT * FROM members ".$order_by;

				$stmt5 = $pdo_conn_login->query($sql);
				
		
		//////////////////////////////////////////////////PAGINATION////////////////////////////////////////////////

		$page_id="";$page_id_out="";$start_rec=""; $pagination="";$pagination_left="";$pagination_right="";

		////////////////GET THE PAGE ID///////////////////////////////////////////////////////////////////////////////////////

		$total_records="";$per_page="";$total_page="";

		$total_records = $stmt5->rowCount();

		///////////////////////////////SET THE MAX NUMBER OF RECORDS TO DISPLAY IN EACH PAGE////////////////////////////////////////////////////////////////////////

		$per_page = 10;


		////////////////////GET THE TOTAL PAGES THAT THE ENTIRE RECORD WILL TAKE///////////////////////////////////////////////////////////////////////////////////// 

		$total_page = ceil($total_records/$per_page);

		//////////////////////////////////////////////



		//////////////GET THE PAGE ID IF THERE IS AN INPUT TO JUMP TO PAGE////////////////////////////

		if(isset($_POST["jump_page"])){
			
			
			if($_POST["page_input"] != "")
				$page_id = preg_replace("#[^0-9]#", $total_page, $_POST["page_input"]);

			
			else
				$page_id = $total_page;
			
			
		}

		//////////////////////ELSE GET THE PAGE ID PASSED/////////////////////////////////////////////

		if(isset($_GET["page_id"])){
			
				if($_GET["page_id"] != "")
					$page_id = preg_replace("#[^0-9]#", $total_page, $_GET["page_id"]);
				

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
		 

				if($action_srch)	
					$prev_page = "<a href='?srt=".$action_srch."&page_id=".$prev_page." '><span><< </span>Prev</a> ";	
				
				else
				$prev_page = "<a href='?page_id=".$prev_page." '><span><< </span>Prev</a> ";	
					
				
				
				for($i=($page_id - 4); $i < $page_id; $i++){
					
					if($i < 1)
						continue;
					
					if($action_srch)	
						$pagination_left .= "<a href='?srt=".$action_srch."&page_id=".$i." '>".$i."</a> ";	
					
					else
						$pagination_left .= "<a href='?page_id=".$i." '>".$i."</a> ";	
					
					
			}
				
		}


			 
		///////////////////ONLY DISPLAY THE NEXT  PAGE NAVIGATOR WHEN THERE IS ACTUALLY A NEXT PAGE /////////////////////////////////////////////////////////////////////////////////////
			 
				if($page_id != $total_page){
					
					
		////////////////////DEFINE NEXT_PAGE/////////////////////////////////////////////////////////////////////////////////////
		 
		 $next_page = $page_id + 1;
		 
				if($action_srch)		
					$next_page = "<a href='?srt=".$action_srch."&page_id=".$next_page."' >Next<span> >></span></a> ";	
					
				else
					$next_page = "<a href='?page_id=".$next_page."' >Next<span> >></span></a> ";	
				
				for($i=$page_id + 1; $i <= ($page_id + 4); $i++ ){
					
				if($action_srch)		
					$pagination_right .="<a href='?srt=".$action_srch."&page_id=".$i." '>".$i."</a> ";	
					
				else
					$pagination_right .="<a href='?page_id=".$i." '>".$i."</a> ";	
			
				if($i == $total_page)
					break;
			
			}

				
		}


		//////////////DEFINE FIRST PAGE////////////////////////////////////////////////////////////////////////////////////////////	 
			 
			 if($page_id > 1){
			 $first_page = 1;
			 
			 if($action_srch)	
				$first_page = "<a href='?srt=".$action_srch."&page_id=".$first_page." '>First</a> ";	
				
			else
				$first_page = "<a href='?page_id=".$first_page." '>First</a> ";	
			
		}	
		///////DEFINE LAST PAGE///////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			if($page_id != $total_page)	{
				
				$last_page = $total_page;
				
				if($action_srch)	
					$last_page = "<a href='?srt=".$action_srch."&page_id=".$last_page." '>Last</a> ";	
					
				else
					$last_page = "<a href='?page_id=".$last_page." '>Last</a> ";	

		}
		//////////////////////GENERATE THE FINAL PAGINATION BEHAVIOR////////////////////////////////////////////////////////////////////////////////////
			 
			 
			 $pagination = $first_page.$prev_page.$pagination_left."<span id=current_page>".$page_id."</span> ".$pagination_right.$next_page.$last_page." <form class=jump2page  method=post action='?page_id='><li class=jump2page_wrapper id=jump2page_wrapper ><input type=text name=page_input /><input class=jump2page_button id=jump2page_button type=submit name=jump_page value='Jump to page' /></li><input type='hidden' name='srt' value='".$action_srch."'  /></form><a  id='skippage' title='jump to page' onclick='return false;' href='#' class='skippage links'><img class='pageskip' src='wealth-island-images/icons/skippage.png' alt='icon' /></a>";
			 
			 
		 }

		/////////////////////////////////END OF PAGINATION/////////////////////////////////////////////////////////////////	
		
///////////PDO QUERY////////////////////////////////////
			
				$sql =  "SELECT * FROM members ".$order_by." LIMIT ".$start_rec.",".$per_page;

				$stmt6 = $pdo_conn_login->query($sql);
				
		
		if($stmt6->rowCount()){
		
			while($user = $stmt6->fetch(PDO::FETCH_ASSOC)){
				
				if(strtolower($user["USERNAME"]) == "seer" && $privilege != "ADMIN")
					continue;
			
				$output_all .= 	"<caption><h1 class='h_bkg'>".strtoupper(trim($user["USERNAME"]))."'s DATAS</h1></caption>
								<table class='search_user_table'>
				
										<tr>
											<th>USER ID</th>
											
											<td>".$user["ID"]."</td>
										
										</tr>
				
				
										<tr>
											<th>USERNAME</th>
											
											<td>".$user["USERNAME"]."</td>
										
										</tr>
				
				
										<tr>
											<th>EMAIL</th>
											
											<td>".$user["EMAIL"]."</td>
										
										</tr>
				
				
										<tr>
											<th>FULL NAME</th>
											
											<td>".ucfirst($user["FULL_NAME"])."</td>
										
										</tr>
				
				
										<tr>
											<th>GENDER</th>
											
											<td>".$user["GENDER"]."</td>
										
										</tr>
				
				
										<tr>
											<th>REGISTERED ON:</th>
											
											<td>".dateFormatStyle($user["TIME"])."</td>
										
										</tr>								
										
										<tr>
											<th>AVATAR:</th>
											
											<td>".getDP($user["USERNAME"],"NOLINK")."</td>
										
										</tr>
											
										<tr>
											<th>BIRTHDAY</th>
											
											<td>".$user["DOB"]."</td>
										
										</tr>
										
										<tr>
											<th>ACCOUNT STATUS</th>
											
											<td>".$user["ACCOUNT_STATUS"]."</td>
										
										</tr>
										<tr>
											<th>SUSPENSION STATUS</th>
											
											<td>".$user["SUSPENSION_STATUS"]."</td>
										
										</tr>
										<tr>
											<th>PRIVILEGE</th>
											
											<td>".$user["USER_PRIVILEGE"]."</td>
										
										</tr>
										<tr>
											<th>COUNTRY</th>
											
											<td>".$user["COUNTRY"]."</td>
										
										</tr>
										<tr>
											<th>STATE</th>
											
											<td>".$user["STATE"]."</td>
										
										</tr>
										<tr>
											<th>ADDRESS</th>
											
											<td>".$user["ADDRESS"]."</td>
										
										</tr>
										<tr>
											<th>MARITAL</th>
											
											<td>".$user["MARITAL_STATUS"]."</td>
										
										</tr>
										<tr>
											<th>PHONE 1</th>
											
											<td>".$user["MOBILE_PHONE"]."</td>
										
										</tr>
										<tr>
											<th>PHONE 2</th>
											
											<td>".$user["ALT_MOBILE_PHONE"]."</td>
										
										</tr>
										<tr>
											<th>BANK</th>
											
											<td>".$user["BANK_NAME"]."</td>
										
										</tr>
										<tr>
											<th>ACCOUNT HOLDER</th>
											
											<td>".$user["ACCOUNT_NAME"]."</td>
										
										</tr>
										<tr>
											<th>ACCOUNT NUMBER</th>
											
											<td>".$user["ACCOUNT_NUMBER"]."</td>
										
										</tr>
										<tr>
											<th>CYCLE</th>
											
											<td>".$user["LOOP_STATUS"]."</td>
										
										</tr>
										<tr>
											<th>PACKAGE</th>
											
											<td>".$user["CURRENT_PACKAGE"]."</td>
										
										</tr>
										<tr>
											<th>FDIRECT</th>
											
											<td>".$user["FLOW_DIRECTION"]."</td>
										
										</tr>
										<tr>
											<th>PURGES</th>
											
											<td>".$user["TOTAL_PURGE"]."</td>
										
										</tr>
										<tr>
											<th>DECLINATIONS</th>
											
											<td>".$user["TOTAL_DECL"]."</td>
										
										</tr>
										<tr>
											<th>RECY DEADLINE</th>
											
											<td>".dateFormatStyle($user["RECYCLING_DEADLINE"])."</td>
										
										</tr>
				
							</table>";
						
			}

		}else{
			$output_all = '<div class="errors">Sorry no result was found matching your request</div>';
		}
		

}
	
}
	
else{

$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?ln=Please Login First#lun' class=links>click here to Login first</a>";

}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>MEMBERS</title>
<?php require_once("include-html-headers.php")   ?>

<script></script>

<style>
</style>
</head>

<body>
<div class="wrapper" id="go_up">
	<?php if(isset($_SESSION["username"])) require_once('euromenunav.php') ?>


	<header class="mainnav">
	<a href='<?=$getdomain; ?>' title='General, Entertainment, Science & Technologies'><?=$domain_name;?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>Members </a> "   ;

			
	?>
	</header>

	<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>
	
	<?php echo getMidPageScroll(); ?>


	<?php if($not_logged)   echo "<div class=view_user_wrapper>".$not_logged."</div>";

	if(isset($no_view_privilege))   echo "<div class=view_user_wrapper>".$no_view_privilege."</div>"; 

	?>


	<?php if($_SESSION["username"] && ($privilege == "ADMIN" || $privilege == "MODERATOR")) { ?>

	<div class="view_user_wrapper" id="hide_vuwbb">

		<h1 class="h_bkg">SEARCH A MEMBER</h1>

		<?php if(isset($alert_user))  echo $alert_user; ?>


		<form method="post" action="">

			<label>USERNAME:</label>
			<div id="srch_clr">
						<!--<a href="#" onclick="return false"  id="srch_clrf" class="clearf">&times;</a>-->
						<input  class=' disable_id_field only_form_textarea_inputs'  value="<?php if(isset($uid))  echo $uid ?>"    type="text" name="sq" placeholder="Enter the user you want to search here" /><br/>
			</div>
			<input type="submit"  class="formButtons" name="search" value="SEARCH" />

		</form>


		<?php



		if(isset($output))  echo $output  ;

		if($privilege == "ADMIN" || $privilege == "MODERATOR"){
		
			if(!isset($_POST["view_search"])){
				
				echo "<h1 class='h_bkg'>TABLE OF MEMBERS<br/>(".$total_records." MEMBERS)</h1>";


				if(isset($_GET["srt"])){
					

				$get_sort =	trim(strtolower($_GET["srt"]));

				if($get_sort  == "latest")
							echo "<h1 class=postul>SORT BY:</h1> 
									| <a  class='current_tab' >Latest</a>|
									<a href='?srt=alphabets' class='links ' >Alphabets</a> |
									<a href='?srt=old' class='links ' >Oldest</a> |
									<a href='?srt=s1' class='links ' >Suspended</a> |
									<a href='?srt=a0' class='links ' >Inactive Accounts</a> |
									<a href='?srt=rc' class='links ' >Reg Complete</a> |";
								
				if($get_sort  == "alphabets")
							echo "<h1 class=postul>SORT BY:</h1> 
									| <a  href='?srt=latest' class='links' >Latest</a> |
									<a class='current_tab' >Alphabets</a> |
									<a href='?srt=old' class='links ' >Oldest</a> |
									<a href='?srt=s1' class='links ' >Suspended</a> |
									<a href='?srt=a0' class='links ' >Inactive Accounts</a> |
									<a href='?srt=rc' class='links ' >Reg Complete</a> |";
								
				if($get_sort  == "old")
							echo "<h1 class=postul>SORT BY: </h1>
									| <a  href='?srt=latest' class='links' >Latest</a> |
									<a href='?srt=alphabets' class='links ' >Alphabets</a> |
									<a class='current_tab' >Oldest</a> |
									<a href='?srt=s1' class='links ' >Suspended</a> |
									<a href='?srt=a0' class='links ' >Inactive Accounts</a> |
									<a href='?srt=rc' class='links ' >Reg Complete</a> |";
								
				if($get_sort  == "s1")
							echo "<h1 class=postul>SORT BY:</h1> 
									| <a  href='?srt=latest' class='links' >Latest</a> |
									<a href='?srt=alphabets' class='links ' >Alphabets</a> |
									<a href='?srt=old' class='links ' >Oldest</a> |
									<a class='current_tab' >Suspended</a> |
									<a href='?srt=a0' class='links ' >Inactive Accounts</a> |
									<a href='?srt=rc' class='links ' >Reg Complete</a> |";
								
				if($get_sort  == "a0")
							echo "<h1 class=postul>SORT BY:</h1> 
									| <a  href='?srt=latest' class='links' >Latest</a> |
									<a href='?srt=alphabets' class='links ' >Alphabets</a> |
									<a href='?srt=old' class='links ' >Oldest</a> |
									<a href='?srt=s1' class='links ' >Suspended</a> |
									<a class='current_tab' >Inactive Accounts</a> |
									<a href='?srt=rc' class='links ' >Reg Complete</a> |";
								
				if($get_sort  == "rc")
							echo "<h1 class=postul>SORT BY:</h1> 
									| <a  href='?srt=latest' class='links' >Latest</a> |
									<a href='?srt=alphabets' class='links ' >Alphabets</a> |
									<a href='?srt=old' class='links ' >Oldest</a> |
									<a href='?srt=s1' class='links ' >Suspended</a> |
									<a href='?srt=a0' class='links ' >Inactive Accounts</a> |
									<a class='current_tab' >Reg Complete</a> |";
									
					
				}

				else
					echo "<h1 class=postul>SORT BY: </h1>
									| <a  href='?srt=latest' class='links' >Latest</a> |
									<a class='current_tab' >Alphabets</a> |
									<a href='?srt=old' class='links ' >Oldest</a> |
									<a href='?srt=s1' class='links ' >Suspended</a> |
									<a href='?srt=a0' class='links ' >Inactive Accounts</a> |
									<a href='?srt=rc' class='links ' >Reg Complete</a> |";
									
					
				if(isset($pagination))  echo "<h1>(page <span class=cyan>".$page_id."</span> of ".$total_page." )</h1>";

				if(isset($pagination))  echo "<div class=pagination>".$pagination."</div>";


				 if(isset($output_all))  echo $output_all  ;


				if(isset($pagination))  echo "<div class=pagination>".$pagination."</div>";

			}
		}

		 ?>


	</div>



	<?php } ?>

	<span id="go_down"></span>
	
	<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>