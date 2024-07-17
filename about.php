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

?>

<!DOCTYPE HTML>
<html>
<head>

<title>ABOUT US</title>
<?php require_once('include-html-headers.php')   ?>


<style>

</style>


</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')     ?>

	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		echo "<a href='about' title=>About Us</a> "  ;
				
		?>
	</header>
	
	<div class="view_user_wrapper" id="hide_vuwbb">
		<?php echo getMidPageScroll(); ?>	
	
		<h1 class="h_bkg2"><img class="min_img" src="wealth-island-images/icons/strelka_rt.png" /> ABOUT <?php if (isset($domain_name)) echo strtoupper($domain_name); ?> <img class="min_img" src="wealth-island-images/icons/strelka_lt.png" /> </h1>
		<div class="hiw type_a">	
			<div class="abt_banner"></div>
			<h2 class="h_bkg">ABOUT</h2>
				<?php if(isset($domain_name)) echo $domain_name; ?> was founded by a team of enthusiasts and humanitarian specialists who understands
				that there is need to bridge the gap between the rich and the poor. They  have created this platform to act as a stepping stone in helping 
				you climb up the financial freedom ladder, with a core mutual value that centers primarily on innovativeness, integrity, simplicity and excellence.
				At <?php if(isset($domain_name)) echo $domain_name; ?> we believe that helping one another in a mutual and effective way is the normal norm of life.
					
			<h2 class="h_bkg">VISION/MISSION </h2>
			Our vision is simply to help man kind grow towards his financial freedom by providing means of social-economic empowerment that will help 
			people climb up higher towards their financial breakthrough.
			We believe that there is need to bridge the ridiculous gap between the rich and the poor. 
			<h2 class="h_bkg">CORE VALUES </h2>
			Our core values centers primarily and mutually on innovativeness, integrity, simplicity and excellence.
			We believe that helping one another is the normal norm of life.
			<h2 class="h_bkg">AIM </h2>
				<?php if(isset($domain_name)) echo $domain_name; ?> was created with the particular goal of helping lift the populace above poverty by helping them climb up the financial freedom ladder. 
			Our system connects people from diverse culture and race whom through donations are willing to provide financial assistance to each other. Help one today, Get helped by another Tomorrow. 
			
			<h2>OUR RULES ARE SIMPLE<br/>Please Read the Following carefully</h2>
			<?php echo getRules();  ?>
		</div>	
		<h2 class="h_bkg">" We'll be delighted to help you cross that bridge and climb up the ladder to your financial freedom."</h2>
	</div>
	<?php require_once('eurofooter.php')     ?>
</div>
</body>

</html>


