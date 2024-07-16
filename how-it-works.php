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

?>

<!DOCTYPE HTML>
<html>
<head>

<title>How it Works - Donate and Get 200% Returns on Investment</title>
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

		echo "<a href='how-it-works' title=>How It Works</a> "  ;
				
		?>
	</header>
	
	<div class="view_user_wrapper" id="hide_vuwbb">
		<?php echo getMidPageScroll(); ?>	
	
		<h1 class="h_bkg2"><img class="min_img" src="wealth-island-images/icons/strelka_rt.png" /> HOW IT WORKS <img class="min_img" src="wealth-island-images/icons/strelka_lt.png" /></h1>
		
		<div class="hiw type_a">
		
			<img class="img_type4" src="wealth-island-images/icons/mstcks.png" />
			
					 When you Register an account with us, you will need to select a suitable package and pledge a donation (equivalent to the package investment amount). The system will automatically search for a member in need of help equivalent to the amount you pledged and assign that member to you.
			 Then, you will be required to redeem your pledge by paying the pledged amount directly into the assigned member's  bank account.
			 Once you have redeemed your pledge, you will need to wait for confirmation of your disbursement from the assigned member. After you have received confirmation, you automatically become eligible to receive help from two other members
			 that have also made pledges. These two members will be automatically assigned to you by the system to pay you each 100% of your pledged amount making it a total of 200% as your return investment.
			 
			<h2>OUR RULES ARE SIMPLE<br/>Please Read the Following carefully</h2>
			<?php echo getRules();  ?>
		</div>
		<h2 class="h_bkg">" We'll be delighted to help you cross that bridge and climb up the ladder to your financial freedom."</h2>
	</div>
	<?php require_once('eurofooter.php')     ?>
</div>
</body>

</html>


