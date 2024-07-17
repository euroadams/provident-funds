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

<title>POLICIES/RULES</title>
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

		echo "<a href='policies' title=>Policies</a> "  ;
				
		?>
	</header>
	
	<div class="view_user_wrapper" id="hide_vuwbb">
		<?php echo getMidPageScroll(); ?>	
	
		<h1 class="h_bkg"><img class="min_img" src="wealth-island-images/icons/strelka_rt.png" /> POLICIES/RULES <img class="min_img" src="wealth-island-images/icons/strelka_lt.png" /> </h1>
		<div class="poli_banner">
			<img class="img_type5" src="wealth-island-images/icons/poli-logo1.jpeg" />
			<img class="img_type5" src="wealth-island-images/icons/poli-logo2.jpeg" />
			<img class="img_type5" src="wealth-island-images/icons/poli-logo3.jpeg" />
			<img class="img_type5" src="wealth-island-images/icons/poli-logo4.jpeg" />
		</div>
		<div class="hiw type_a">	

			<?php echo getRules();  ?>
			<span class="red">Please see also our <a href="terms-and-condition" class="links">Terms and Conditions</a></span>
		</div>	
		<h2 class="h_bkg">" We'll be delighted to help you cross that bridge and climb up the ladder to your financial freedom."</h2>
	</div>
	<?php require_once('eurofooter.php')     ?>
</div>
</body>

</html>