<?php
session_start();
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();

setPageTimeZone();

$username = $_SESSION["username"];

if($username){
	
/***************AJAX POST FOR USER RECYCLING DEADLINE*********************************************************/
	
	if(isset($_POST["rqst_recy_time"]))
	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT RECYCLING_DEADLINE FROM members WHERE USERNAME = ?  LIMIT 1";

		$stmt = $pdo_conn_login->prepare($sql);
		$stmt->execute(array($username));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);	

		//echo $row["RECYCLING_DEADLINE"];
		echo Date('2017-02-28');
		exit();


	
}
else{
$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

}



?>


<!DOCTYPE HTML>
<html>
<head>
<title>TIMER</title>
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

			
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper">

				<div class='midpage_scroll'>
									<a class='topagedown' title='scroll to bottom of page' href='#'><img src='eurotech-images/icons/scrolldown.png' /></a>
									<a class='topageup' title='scroll to top of page' href='#'><img src='eurotech-images/icons/scrollup.png' /></a>
				</div>

		<?php if(isset($not_logged))   echo $not_logged    ?>

	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>
