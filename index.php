<?php

session_start();
require_once("phpfunctions.php");

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

$loh="";
///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();
	setPageTimeZone();
	
	
$std_dis="";$clsc_dis="";$prm_dis="";$elt_dis=""; $lrd_dis="";$mst_dis="";$roy_dis="";$ult_dis="";
$std_dis_css="";$clsc_dis_css="";$prm_dis_css="";$elt_dis_css="";
$lrd_dis_css="";$mst_dis_css="";$roy_dis_css="";$ult_dis_css="";
	
$username = $_SESSION["username"];

/***********SET LAUNCH/RESUME DATES******************************/

/*
$ref_time  = 1490135533 ;

$std_launch = ($ref_time  + (0));///0days from 21-03-2017////
$clsc_launch = ($ref_time  + (86400*0));///20days from 21-03-2017////
$prm_launch = ($ref_time  + (86400*0));///20days from 21-03-2017////
$elt_launch = ($ref_time  + (86400*0));///20days from 21-03-2017////

$lrd_launch = ($ref_time  + (86400*45));///20days from 21-03-2017////
$mst_launch = ($ref_time  + (86400*80));///20days from 21-03-2017////
$roy_launch = ($ref_time  + (86400*80));///40days from 21-03-2017////
$ult_launch = ($ref_time  + (86400*80));///40days from 21-03-2017////
*/

$std_launch = packageOpenTime("STANDARD");
$clsc_launch = packageOpenTime("CLASSIC");
$prm_launch = packageOpenTime("PREMIUM");
$elt_launch = packageOpenTime("ELITE");
$lrd_launch = packageOpenTime("LORD");
$mst_launch = packageOpenTime("MASTER");
$roy_launch = packageOpenTime("ROYAL");
$ult_launch = packageOpenTime("ULTIMATE");

	
	
$fp_txt = '<div>Welcome to your world of financial freedom<br/>At '.$domain_name.' we believe there is a need to bridge the gap between the rich and the poor
			<br/>However, we also understand that doing so is not always an easy task and for that reason, we have developed a solid ladder connecting the two sides of the bridge and together we help you cross the bridge
			and climb up the ladder to your financial freedom.<br/><br/>At  '.$domain_name.', your financial freedom is our specialty and your success is our pride and passion.<br/>
			<a class="links all_abtn" href="register">JOIN NOW</a><a class="links all_abtn" href="login">LOGIN</a></div>
			';	
		

//////////////GET TOTAL TESTIMONIALS SO IT CAN BE RANDOMIZED///////////////////////////////////////////////
///////////PDO QUERY////////////////////////////////////	

$sql = "SELECT * FROM letters_of_happiness";
$stmt = $pdo_conn_login->query($sql);

if($stmt->rowCount()){
	
	$start_rec = rand(0, ($stmt->rowCount() - 1));
	if($start_rec < 0)
		$start_rec = 0;	
	$per_page = 10;
	
	$sql = "SELECT * FROM letters_of_happiness ORDER BY TIME DESC LIMIT ".$start_rec.",".$per_page;
	$stmt1 = $pdo_conn_login->query($sql);
	if($stmt1->rowCount()){
		
		while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
			
				$loh .= '
						<div class="clear"><span class="">'.getDP($row["SENDER"],"NOLINK").'</span><span class="loh_header">'.$row["FULL_NAME"].'<br/><span class="loh_loc">'.$row["LOCATION"].'</span></span></div>
						<div class="loh_content">'.$row["CONTENT"].'</div><div class="clear"><span class="loh_footer">'.dateFormatStyle($row["TIME"]).'</span></div><hr/>';
			
		}
		
		$loh = substr($loh, 0, -5);

	}

}


?>


<!DOCTYPE HTML>
<html lang="en-us">
<head>
<title><?=$domain_name; ?> - Your Financial Freedom -  Donate and Get 200% Returns on Investment</title>
<meta name="keywords" content="<?php if(isset($domain_name)) echo $domain_name?>, Get Rich, Best Foundation, Climbing up financial ladder, Financial freedom, Donation, Fund raising, Giving and Receiving, Charity, Community, Wealth Creation, Time Freedom, Making Money, Money Making Machine"/>
<meta name="description" content="<?php if(isset($domain_name)) echo $domain_name?> fast peer-to-peer donation platform, make 200% on or before 21 days as your return on investment on any of our packages. At <?php if(isset($domain_name)) echo $domain_name?> we help you climb up the financial freedom ladder.">
<?php require_once("include-html-headers.php")   ?>

<?php
	
	$user_priv = getUserPrivilege($username);
	
	if($std_launch > time()){
				
		echo '<script>startStandardCountDown('.$std_launch.')</script>';
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
				
			$std_dis = 'disabled';
			$std_dis_css = 'disabled';
		}
	}
	if($clsc_launch  > time()){
				
		echo '<script>startClassicCountDown('.$clsc_launch.')</script>';
		
		
		if($user_priv != "ADMIN"  && $user_priv != "FORCED"){
				
			$clsc_dis = 'disabled';
			$clsc_dis_css = 'disabled';
		}
	}
	if($prm_launch  > time()){
				
		echo '<script>startPremiumCountDown('.$prm_launch.')</script>';
		
		
		if($user_priv != "ADMIN" && $user_priv != "FORCED"){
					
			$prm_dis = 'disabled';
			$prm_dis_css = 'disabled';
		}
	}
	if($elt_launch  > time()){
				
		echo '<script>startEliteCountDown('.$elt_launch.')</script>';
		
		if($user_priv != "ADMIN"  && $user_priv != "FORCED"){
			
			$elt_dis = 'disabled';
			$elt_dis_css = 'disabled';
		}
			
	}
	if($lrd_launch > time()){
				
		echo '<script>startLordCountDown('.$lrd_launch.')</script>';
		if($user_priv != "ADMIN"  && $user_priv != "FORCED"){
			
			$lrd_dis = 'disabled';
			$lrd_dis_css = 'disabled';
		}
	}
	if($mst_launch  > time()){
				
		echo '<script>startMasterCountDown('.$mst_launch.')</script>';
		
		if($user_priv != "ADMIN"  && $user_priv != "FORCED"){
			$mst_dis = 'disabled';
			$mst_dis_css = 'disabled';
		}
	}
	if($roy_launch  > time()){
				
		echo '<script>startRoyalCountDown('.$roy_launch.')</script>';
		
		if($user_priv != "ADMIN"  && $user_priv != "FORCED"){
			$roy_dis = 'disabled';
			$roy_dis_css = 'disabled';
		}
	}
	if($ult_launch  > time()){
				
		echo '<script>startUltimateCountDown('.$ult_launch.')</script>';
		
		if($user_priv != "ADMIN"  && $user_priv != "FORCED"){
			$ult_dis = 'disabled';
			$ult_dis_css = 'disabled';
		}
	}


?>

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

		echo "<a href='".$page_self."'>Home</a> "  ;
		
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<?php echo getMidPageScroll(); ?>	

		<div class="fp_wrap">			
			<div class="fp1">
				<h1 class="lgreen fp_header"><?php if(isset($domain_name)) echo strtoupper($domain_name); ?></h1>
				<?php echo $fp_txt; ?>
				
			</div><br/>
			 <div class="slideshow-container">
			  <div class="mySlides">
				<div class="numbertext">1 / 4</div>
				<img alt="slide" src="wealth-island-images/icons/poli-logo1.jpeg" />
				<div class="text">INTELLIGENT ALGORITHM</div>
			  </div>
			  <div class="mySlides">
				<div class="numbertext">2 / 4</div>
				<img alt="slide" src="wealth-island-images/icons/poli-logo2.jpeg" />
				<div class="text">OUTSTANDING TEAM OF ENGINEERS</div>
			  </div>
			  <div class="mySlides">
				<div class="numbertext">3 / 4</div>
				<img alt="slide" src="wealth-island-images/icons/poli-logo3.jpeg" />
				<div class="text">EXCELLENT TEAM EFFORTS</div>
			  </div>
			  <div class="mySlides">
				<div class="numbertext">4 / 4</div>
				<img alt="slide" src="wealth-island-images/icons/poli-logo4.jpeg" />
				<div class="text">SUSTAINABLE FINANCIAL GROWTH</div>
			  </div>

			  <a class="prev" >&#10094;</a>
			  <a class="next">&#10095;</a>
			</div>
			<div class="pos_ind">
			  <span class="dot" data="0"></span>
			  <span class="dot" data="1"></span>
			  <span class="dot" data="2"></span>
			  <span class="dot" data="3"></span>
			</div>
			<br/>
			<img alt="logo" class="" src="wealth-island-images/icons/pf-logo-1.png" /> 
			<div id="packages_h">				
				<h1 class="h_bkg"><img alt="icon" class="min_img" src="wealth-island-images/icons/strelka_dwn.png" /> PACKAGES WE OFFER <img alt="icon" class="min_img" src="wealth-island-images/icons/strelka_dwn.png" /> </h1>
				<h3 class="red">Please only select a package that you are comfortable with and that corresponds to the cash you have at hand</h3><br/><br/>				
				<?php if(packageVisibility("STANDARD")){?>
				<div class="packages std_pack">
					<h1>STANDARD <?php echo getPackageFollowers("STANDARD"); ?></h1>
					<h2>Donate<br/> ₦5,000</h2><hr/>
					<?php echo getPackFeats(); ?>
					<h2>Get<br/> ₦10,000</h2>					
					<form action="gate-check" method="post">
						<input type="submit" name="start" class="formButtons  <?php echo $std_dis_css ?>"  <?php echo $std_dis ?> id="std_btn" value="JOIN" />
					</form>
					<div class="std_timer_wrapper"></div>
				</div>
				<?php } ?>
				<?php if(packageVisibility("CLASSIC")){?>
				<div class="packages clsc_pack">
					<h1>CLASSIC <?php echo getPackageFollowers("CLASSIC"); ?></h1>
					<h2>Donate<br/> ₦10,000</h2><hr/>
					<?php echo getPackFeats(); ?>
					<h2>Get<br/> ₦20,000</h2>					
					<form action="gate-check" method="post">				
						<input type="submit" name="start" class="formButtons  <?php echo $clsc_dis_css ?>"  <?php echo $clsc_dis ?> id="clsc_btn" value="JOIN" />
					</form>	
					<div class="clsc_timer_wrapper"></div>					
				</div>
				<?php } ?>
				<?php if(packageVisibility("PREMIUM")){?>
				<div class="packages prm_pack">
					<h1>PREMIUM <?php echo getPackageFollowers("PREMIUM"); ?></h1>
					<h2>Donate<br/> ₦20,000</h2><hr/>
					<?php echo getPackFeats(); ?>
					<h2>Get<br/> ₦40,000</h2>					
					<form action="gate-check" method="post">
						<input type="submit" name="start" class="formButtons  <?php echo $prm_dis_css ?>"  <?php echo $prm_dis ?> id="prm_btn" value="JOIN" />
					</form>
					<div class="prm_timer_wrapper"></div>
				</div>
				<?php } ?>
				<?php if(packageVisibility("ELITE")){?>
				<div class="packages elt_pack">
					<h1>ELITE <?php echo getPackageFollowers("ELITE"); ?></h1>
					<h2>Donate<br/> ₦50,000</h2><hr/>
					<?php echo getPackFeats(); ?>
					<h2>Get<br/> ₦100,000</h2>						
					<form action="gate-check" method="post">
						<input type="submit" name="start" class="formButtons  <?php echo $elt_dis_css ?>"  <?php echo $elt_dis ?> id="elt_btn" value="JOIN" />
					</form>
					<div class="elt_timer_wrapper"></div>
				</div>
				<!--<br/><br/><h1 class="h_bkg"><img class="min_img" src="wealth-island-images/icons/strelka_up.png" /> UPCOMING PACKAGES <img class="min_img" src="wealth-island-images/icons/strelka_up.png" /></h1>-->
				<?php } ?>
				<?php if(packageVisibility("LORD")){?>
				<div class="packages lrd_pack">
					<h1>LORD <?php echo getPackageFollowers("LORD"); ?></h1>
					<h2>Donate<br/> ₦100,000</h2><hr/>
					<?php echo getPackFeats(); ?>
					<h2>Get<br/> ₦200,000</h2>						
					<form action="gate-check" method="post">
						<input type="submit" name="start" class="formButtons  <?php echo $lrd_dis_css ?>"  <?php echo $lrd_dis ?> id="lrd_btn" value="JOIN" />
					</form>
					<div class="lrd_timer_wrapper"></div>
				</div>
				<?php } ?>
				<?php if(packageVisibility("MASTER")){?>
				<div class="packages mst_pack">
					<h1>MASTER <?php echo getPackageFollowers("MASTER"); ?></h1>
					<h2>Donate<br/> ₦200,000</h2><hr/>
					<?php echo getPackFeats(); ?>
					<h2>Get<br/> ₦400,000</h2>						
					<form action="gate-check" method="post">
						<input type="submit" name="start" class="formButtons  <?php echo $mst_dis_css ?>"   <?php echo $mst_dis ?> id="mst_btn" value="JOIN" />
					</form>
					<div class="mst_timer_wrapper"></div>
				</div>
				<?php } ?>
				<?php if(packageVisibility("ROYAL")){?>
				<div class="packages roy_pack">
					<h1>ROYAL <?php echo getPackageFollowers("ROYAL"); ?></h1>
					<h2>Donate<br/> ₦500,000</h2><hr/>
					<?php echo getPackFeats(); ?>
					<h2>Get<br/> ₦1,000,000</h2>						
					<form action="gate-check" method="post">				
						<input type="submit" name="start" class="formButtons  <?php echo $roy_dis_css ?>"  <?php echo $roy_dis ?> id="roy_btn" value="JOIN" />
					</form>
					<div class="roy_timer_wrapper"></div>
				</div>
				<?php } ?>
				<?php if(packageVisibility("ULTIMATE")){?>
				<div class="packages ult_pack">
					<h1>ULTIMATE <?php echo getPackageFollowers("ULTIMATE"); ?></h1>
					<h2>Donate<br/> ₦1,000,000</h2><hr/>
					<?php echo getPackFeats(); ?>
					<h2>Get<br/> ₦2,000,000</h2>					
					<form action="gate-check" method="post">				
						<input type="submit" name="start" class="formButtons  <?php echo $ult_dis_css ?>"  <?php echo $ult_dis ?> id="ult_btn" value="JOIN" />
					</form>		
					<div class="ult_timer_wrapper"></div>
				</div>
				<?php } ?>				
			</div>
			<div>
				<!--<img class="steps_img" src="wealth-island-images/icons/atmc.jpeg" />-->
			</div>
			<h1><img alt="icon" class="min_img" src="wealth-island-images/icons/strelka_rt.png" /> See What People are saying about us <img alt="icon" class="min_img" src="wealth-island-images/icons/strelka_lt.png" /></h1>	
			<div class="loh"><h2>TESTIMONIALS</h2>
				<?php   if($loh) echo $loh; ?>
			</div>
			<div class="md_1">
				<h1 class="lgreen">Want to know how <?php if(isset($domain_name)) echo $domain_name; ?> Works?</h1>
				<h3 class="blue">Donate and get 200% of your Initial Investment.<br/><br/>Please See</h3> 
				<a href="how-it-works"  class="links all_abtn">HOW IT WORKS</a>  <a href="testimonials"  class="links all_abtn">TESTIMONIES</a>  <a href="referral-system"  class="links all_abtn">REFERRALS</a>
			</div>
				<?php echo getLatestNews();?>
			
			<div>
				<h2 class="h_bkg"> Together we'll help you cross that bridge and climb up the ladder.</h2>
				<img alt="image" class="img_md" src="wealth-island-images/icons/ladd_frd.jpg" />
			</div>
		</div>			
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>
