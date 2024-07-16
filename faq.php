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
<title>FAQ</title>
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

			echo "<a href='".$page_self."'>FAQ </a> "  ;
		
			
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>	
			
			
		<h1  class="h_bkg"><img class="min_img" src="wealth-island-images/icons/strelka_rt.png" /> FREQUENTLY ASKED QUESTIONS <img class="min_img" src="wealth-island-images/icons/strelka_lt.png" /></h1>
		<div class="hiw type_a">	
			Please forward all questions not treated below to <a class="links" href="contact-support">Support</a> and we'll be glad to answer all your questions.
			<ol id="faq">
				<li>
					<a href="faq#1"><span class="faq_num">1.</span>What is  <?php if(isset($domain_name)) echo $domain_name; ?>?</a>
					<div class="faq_c" id="1">
						<?php if(isset($domain_name)) echo $domain_name; ?> is a peer-to-peer donation and mutual aid fund scheme that creates an avenue for participants to help each other 
						willingly in a mutual manner. By using this scheme, participants voluntarily gives and receives financial assistance
						from each other.
					</div>
				</li>
				<li>
					<a href="faq#2"><span class="faq_num">2.</span>The AIM of  <?php if(isset($domain_name)) echo $domain_name; ?>?</a>
					<div  class="faq_c" id="2">
						<?php if(isset($domain_name)) echo $domain_name; ?> was created with the particular goal of helping lift the populace above poverty by helping them
						climb up the financial freedom ladder.
						Our system connects people from diverse culture and race whom through donations are willing to provide financial assistance to each other.
						Help one today, Get helped by another Tomorrow.						
					</div>
				</li>
				<li>
					<a href="faq#3"><span class="faq_num">3.</span>Is <?php if(isset($domain_name)) echo $domain_name; ?> legal?</a>
					<div class="faq_c" id="3">
						<?php if(isset($domain_name)) echo $domain_name; ?> is a multi-level social network marketing platform where people come together voluntarily
						to give help and also receive help with a mutual financial relationship.<br/>Giving money to a participant in need of it and also 
						receiving money from a participant who is willing to give is not prohibited by either international or local legal systems.<br/>On this note 
						i think it suffice to say <?php if(isset($domain_name)) echo $domain_name; ?> is'nt illegal.
					</div>
				</li>
				<li>
					<a href="faq#4"><span class="faq_num">4.</span>Is there any setup fee?</a>
					<div class="faq_c" id="4">
						NO, <?php if(isset($domain_name)) echo $domain_name; ?> has been dedicated 100% free of charge by a team of humanitarian specialists and enthusiast to help lift the general populace above poverty.
					</div>
				</li>
				<li>
					<a href="faq#5"><span class="faq_num">5.</span>Who is eligible to join  <?php if(isset($domain_name)) echo $domain_name; ?>?</a>
					<div class="faq_c" id="5">
						At <?php if(isset($domain_name)) echo $domain_name; ?>, we welcome everyone. There is no age and gender barrier. Equal benefits and donations are assigned to all participants irrespective of age, gender or race.<br/>
						All that is required is your ability to navigate the internet through mobile or desktop browsers.
					</div>
				</li>
				<li>
					<a href="faq#6"><span class="faq_num">6.</span>What is AVN?</a>
					<div class="faq_c" id="6">
						Upon successful registration and activation of account, Every participant will be issued with an Account Verification Number(AVN) which will be 
						used for virtually all transactions. Your AVN will be sent to your email and also displayed on your dashboard, please take secured measures to keep it safe.
						<br/>You can very well consider your AVN as a One Time Password(OTP)	usually issued by banks to authenticate a transaction.						
						Although your AVN is kept confidential for security reasons, the overall responsibility of  keeping your AVN safe solely falls on you.
					</div>
				</li>
				<li>
					<a href="faq#7"><span class="faq_num">7.</span>Can I have multiple accounts?</a>
					<div class="faq_c" id="7">
						NO. The system is strictly against multiple accounts and circumventions.
						It is not possible for you to be in need of help and at the same time expect the helper to be you. The system is strictly for those
						in need of help from others who are willing to give it and not for those seeking help from themselves.<br/>
						Any account flagged by the system as multiple will be automatically deleted without notice. 
					</div>
				</li>	
				<li>
					<a href="faq#8"><span class="faq_num">8.</span>What is Dashboard?</a>
					<div class="faq_c" id="8">
						The Dashboard is simply your personal office where you work on the platform. All transactions(incoming and outgoing) are done here as well as
						membership/account management, Customer Relationship management and security management.
					</div>
				</li>
				<li>
					<a href="faq#9"><span class="faq_num">9.</span>How can I Join?</a>
					<div class="faq_c" id="9">
						You can join through a participant's referral link by clicking on it, which will take you directly to
						 <?php if(isset($domain_name)) echo $domain_name; ?></a> registration page where you will be asked to confirm your
						 e-mail and thereafter a registration form will be presented for you to fill. If you are signing up through a referral
						 link make sure the username of your referral is showing in the referral field of the registration form.<br/>
						<br/>
						All participants are advised to share their referral links on social medias(facebook, instagram, twitter, whatsapp .... ) because you
						will be rewarded with 500NgN for every participant you refer that actually pledges a donation and redeems it.
						To know more about referrals, please see <a class="links" href="referral-system">our referral system</a>.
						<br/><br/>
						
						In the event that you were not invited by any participant, then you can join by simply 
						clicking on the Join Now button on our homepage.
					</div>
				</li>
				<li>
					<a href="faq#10"><span class="faq_num">10.</span>What is the difference between the packages?</a>
					<div class="faq_c" id="10">
						The only difference in the packages are in their capital and investment return amounts. You can simply say the higher your package, the more returns on investment you can earn. 
					</div>
				</li>				
				<li>
					<a href="faq#11"><span class="faq_num">11.</span>How many packages can i join at a time?</a>
					<div class="faq_c" id="11">
						You can only join one package at a time. Once your package cycle is complete then you can change to any other package if you like.
					</div>
				</li>
				<li>
					<a href="faq#12"><span class="faq_num">12.</span>I have made disbursement but it's yet to be confirmed?</a>
					<div class="faq_c" id="12">
						There is always a contact information included in every new order dispatched to your dashboard, Please always copy 
						the informations of the participant paired with you so that you can contact him/her after making disbursement 
						to confirm the receipt immediately. All transactions are strictly between participants only(please see <a class="links" href="terms-and-condition">Terms and Conditions</a>). <br/><br/>If a participant fails to 
						confirm your disbursement and you have tried contacting him/her, then submit a support
						ticket and it will be resolved judiciously.						
					</div>
				</li>
				<li>
					<a href="faq#13"><span class="faq_num">13.</span>If the person assigned to pay me didnt pay, what next?</a>
					<div class="faq_c" id="13">
						As long as your pledge has been confirmed and the system assigned you before, the system will automatically remove
						the participant assigned to pay you once his/her deadline elapses  and immediately Reassign you to another participant.						
					</div>
				</li>								
				<li>
					<a href="faq#14"><span class="faq_num">14.</span>How do I receive my payment?</a>
					<div class="faq_c" id="14">				
						Once you have redeemed your pledge and have received disbursement confirmation, then you automatically become eligible 
						to receive your 200% returns on investment from 2 donors that will each disburse 100% of your redeemed amount into your bank account
						either through bank payment, mobile transfer or by cash.Please always ensure you have been credited in your bank account before making any disbursement confirmation.
					</div>
				</li>			
				<li>
					<a href="faq#15"><span class="faq_num">15.</span>Is There A Referral Bonus?</a>
					<div class="faq_c" id="15">				
						YES. There are referral bonuses for every referred participant that makes a pledge and redeems it.<br/>
						To know more please see <a class="links" href="referral-system">our referral system</a>.
					</div>
				</li>			
			</ol>			
		</div>	
		<h2 class="h_bkg">" We'll be delighted to help you cross that bridge and climb up the ladder to your financial freedom."</h2>			
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>