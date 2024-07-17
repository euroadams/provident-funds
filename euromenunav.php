<?php

require_once('phpfunctions.php');


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
$getdomain = getDomain();
$domain_name = getDomainName();


/****MAKE SURE THAT ALL DEFAULTERS ARE HANDLED SWIFTLY BY CALLING THIS FUNCTION CONTINOUSLY****************/
handleDefaulters();
doFirstMatching();
	

$menunav=$membersTotal=$dp=$username_x=$dropdown_nav=$show_priv=$privilege_shown=$privilege="";

if(isset($_SESSION["username"]))
	$username_x = $_SESSION["username"];


/////////////////GET THE USER PRIVILEGE///////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
$privilege = getUserPrivilege($username_x);

if($privilege == "ADMIN")
	$privilege_shown = "<span class='lgreen'>**".$privilege."**</span>";

elseif($privilege == "MODERATOR")
	$privilege_shown = "<span class='cyan'>*".$privilege."*</span>";

 if(isset($privilege) && $privilege != "MEMBER")
	  $show_priv = '<br/>'.$privilege_shown;
	
if($privilege != "ADMIN"){
				
	/******TURN OFF ERRORS*******E_ALL,E_PARSE,E_NOTICE,E_WARNING**********************/
	error_reporting(E_ALL);
	ini_set('display_errors',false);
	ini_set('log_errors',true);
	
}else{
	
	/******TURN OFF ERRORS*******E_ALL,E_PARSE,E_NOTICE,E_WARNING**********************/
	error_reporting(E_ALL);
	ini_set('display_errors',true);
	ini_set('log_errors',false);
	
	
}

		if($username_x){
	
///////////////////////////////FETCH TOTAL MESSAGE IN USER'S INBOX/////////////////////////////////////////////////////////////////////////////////////////////

///////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT INBOX FROM privatemessage WHERE USERNAME = ?   AND INBOX_STATUS !='read'" ;

				$stmt = $pdo_conn_login->prepare($sql);

				$stmt->execute(array($username_x));
					
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				
				$mssgnumber = $stmt->rowCount();

				if($mssgnumber)
					$alertinbox = "<div id='cyan' class='errors blink postul'>(You have ".$mssgnumber. " new message(s) in your <a href='inbox' class='links'>Inbox</a>)</div>";


			
///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT * FROM members WHERE USERNAME=? LIMIT 1";

			$stmt1 = $pdo_conn_login->prepare($sql);

			$stmt1->execute(array($username_x));
			
			$row = $stmt1->fetch(PDO::FETCH_ASSOC);
			
			$dashboard_name = $row["FULL_NAME"];
			$email = '('.$row["EMAIL"].')';
			
			
			
			
			
			$dropdown_nav = '
							
							<li><a class="links" href="dash-board" >DASHBOARD</a></li>							
							<li class="udash_wrap">
								<a class="links udash" href="javascript:void(0)" >'.$dashboard_name.'  <img class="drop_ind" src="wealth-island-images/icons/drop_ico.png" alt="icon" /></a>
								<ul>
									<li><a class="links" href="inbox" >Inbox('.$mssgnumber.')</a></li>
									<li><a class="links" href="edit-profile" >Profile</a></li>
									<li><a class="links" href="changepassword" >Change Password</a></li>							
									<li><a class="links" href="provide-help" >Make Donation(PH)</a></li>							
									<li>
										<a class="links udash" href="javascript:void(0)">Histories <img class="drop_ind" src="wealth-island-images/icons/drop_ico.png" alt="icon" /></a>
										<ul>							
											<li><a class="links" href="donation-histories" >Donations</a></li>
											<li><a class="links" href="transaction-histories" >Transaction</a></li>									
											
										</ul>
									</li>
									<li><a class="links" href="referrals" >Referrals</a></li>
									<li><a class="links" href="support-tickets" >Support Tickets</a></li>
									<li>
										<a class="links udash" href="javascript:void(0)">Tetimonial <img class="drop_ind" src="wealth-island-images/icons/drop_ico.png" alt="icon" /></a>
										<ul>							
											<li><a title="View your testimonies" class="links" href="user-testimonials" >My Testimonials</a></li>
											<li><a title="Write a testimony" class="links" href="loh" >Write New testimonial</a></li>									
											
										</ul>
									</li>									
									<li><a class="links" href="logout" >Logout</a></li>
								</ul>
							</li>
							';
		
			}
			
			$dropdown_nav .= '
							<li class="udash_wrap">
								<a class="links udash" href="javascript:void(0)" >'.$domain_name.' <img class="drop_ind" src="wealth-island-images/icons/drop_ico.png" alt="icon" /></a>
								<ul>							
									<li><a class="links" href="how-it-works" >How It Works</a></li>
									<li><a class="links" href="testimonials" >Testimonials</a></li>									
									<li><a class="links" href="about" >About Us</a></li>
									<li><a class="links" href="contact-support" >Contact</a></li>
									<li><a class="links" href="faq" >FAQ</a></li>	
								</ul>
							</li>
							';
			
							
			if(!$username_x){			
			$dropdown_nav.=	'
							<li><a class="links" href="register" >Register</a></li>
							<li><a class="links" href="login" >Login</a></li>														
							';
							
			}	
										
								
			if($privilege == 'ADMIN' || $privilege == 'MODERATOR'){
				
				$dropdown_nav.=	'<h2 class="lgreen">TEAMS:</h2>
								<li class="udash_wrap">
									<a class="links udash" href="javascript:void(0)" >Team Tools  <img class="drop_ind" src="wealth-island-images/icons/drop_ico.png" alt="icon" /></a>
									<ul>	
										<li><a class="links" href="support-teams" >Support Teams</a></li>
										<li><a class="links" href="reports" >Report Teams</a></li>
										<li><a class="links" href="members" >Members</a></li>
									</ul>
								</li>
										
							';
				
				
			}					
			if($privilege == 'ADMIN' && strtolower($username_x) == "seer"){
				
				$dropdown_nav.=	'<h2 class="lgreen">ADMINS:</h2>
								<li class="udash_wrap">
									<a class="links udash" href="javascript:void(0)" >Admin Tools  <img class="drop_ind" src="wealth-island-images/icons/drop_ico.png" alt="icon" /></a>
									<ul>	
										<li><a class="links" href="write-news" >Write News</a></li>
										<li><a class="links" href="match-permit" >Authorize Matching</a></li>
										<li><a class="links" href="donations" >Donations</a></li>
										<li><a class="links" href="investment-return-match" >Run IRM</a></li>
										<li><a class="links" href="avn-reset" >AVN Reset</a></li>
										<li><a class="links" href="euro-secured-rematch-purges" >PT Re-Match</a></li>
										<li><a class="links" href="euro-secured-query-page" >Run Query</a></li>
										<li><a class="links" href="package-launcher" >Package Launcher</a></li>										
										<li><a class="links" href="db-manager" >DB Manager</a></li>										
									</ul>
								</li>
										';
						
				
			}
			elseif($privilege == 'ADMIN' && strtolower($username_x) != "seer"){
				
				$dropdown_nav.=	'<h2 class="lgreen">ADMINS:</h2>
								<li class="udash_wrap">
									<a class="links udash" href="javascript:void(0)" >Admin Tools  <img class="drop_ind" src="wealth-island-images/icons/drop_ico.png" alt="icon" /></a>
									<ul>	
										<li><a class="links" href="write-news" >Write News</a></li>
										<li><a class="links" href="match-permit" >Authorize Matching</a></li>
										<li><a class="links" href="donations" >Donations</a></li>										
										<li><a class="links" href="avn-reset" >AVN Reset</a></li>										
										<li><a class="links" href="package-launcher" >Package Launcher</a></li>										
									</ul>
								</li>
										';
						
				
			}
			

?>

<header class="top_nav clear" >
<h2 class="fp_capt"><a id="fp_h"  title="Helping you cross the wealth bridge " href="<?=$getdomain;  ?>"><img alt="logo" class="img_typef" src="wealth-island-images/icons/pf-logo-0.png" /> </a>
<br/><span id="db_name"><?php if(isset($dashboard_name)) echo '<a id="red" href="dash-board" class="links">DashBoard</a> - <a class="links" id="blue" href="edit-profile">'.$dashboard_name.'</a> '.$show_priv.' (<a class="links" href="logout">Logout</a>)'; ?></span>
</h2>
<ul class="clear">
<?php echo $menunav  ?>


<li>
	
	<!--<a id="slide_dropicon" class="open_slide_ico" title="slide navigation menu left" href="#" onclick="return false;" ><img id="dropmenu" alt="icon" src="wealth-island-images/icons/dropmenu.png" /></a> 
	<a id="slide_dropicon" class="close_slide_ico1" title="slide navigation menu left" href="#" onclick="return false;" ><img id="dropmenu" alt="icon" src="wealth-island-images/icons/closemenu.png" /></a> -->
	<a id="drop_dropicon" class="open_drop_ico" title="slide navigation menu down" href="#" onclick="return false;" ><img id="dropmenu" alt="icon" src="wealth-island-images/icons/dropmenu.png" /></a> 
	<a id="drop_dropicon" class="close_drop_ico1" title="slide navigation menu up" href="#" onclick="return false;" ><img id="dropmenu" alt="icon" src="wealth-island-images/icons/closemenu.png" /></a> 
<!--	<a id="top_nav_dropicon" title="slide navigation menu down" href="#" onclick="return false;" ><img id="dropmenu" alt="icon" src="wealth-island-images/icons/dropmenu.png" /></a> -->

</li>
	

<?php echo getDP($username_x,"LINK"); ?>

</ul>

</header>

<nav class="top_nav_drop">
	<ul class="dropmenu">
			<li>
				<h1 id="lgreen">welcome <span class="cyan"><?php echo $username_x; if(isset($email)) echo '<br/>'.$email; ?></span></h1><hr/>
				<a id="drop_dropicon" class="close_drop_ico2" title="slide navigation menu up" href="#" onclick="return false;" ><img id="dropmenu" alt="icon" src="wealth-island-images/icons/closemenu.png" /></a> 
			</li>
			<?= $dropdown_nav; ?>

	</ul> 
</nav>	
	
<!--<nav class="slide_drop_wrapper">
	<ul class="slide_drop">
		<li>
			<h1 id="lgreen">welcome <span class="cyan"><?php echo $username_x; if(isset($email)) echo '<br/>'.$email; ?></span></h1><hr/>
			<a id="slide_dropicon" class="close_slide_ico2" title="slide navigation menu left" href="#" onclick="return false;" ><img id="dropmenu" alt="icon" src="wealth-island-images/icons/closemenu.png" /></a> 
		</li>
		<?= $dropdown_nav; ?>

	</ul>
</nav>-->

