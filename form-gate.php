<?php  
 
session_start();
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
$getdomain = getDomain();
$domain_name = getDomainName();

$rdr_to="";

$username = $_SESSION["username"];

if(isset($_GET["response"]) && isset($_GET["rdr"])){
	
	
	$rdr = $_GET["rdr"];
	$response = $_GET["response"];
	
	//////SET THE RESPONSE COOKIE TO EXPIRE IN 30MIN/////////////////////////////////////////////////
	setcookie("form_gate_response", $response, (time() + 1800));
	
	header("location:".$rdr);
	exit();
	
	
}//////REDIRECT INTRUDERS TO PAGE ERROR////////////////////////////////
else{
	
	header("location:page-error");
	exit();
	
}


 
 ?>
 
 
 
<!DOCTYPE HTML>
<html>
<head>
<title>FORM GATE</title>
<?php 
	
	require_once("include-html-headers.php");

 ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>Form Gate</a> "  ;
			
	?>
	</header>
		
	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<?php getMidPageScroll(); ?>

		<h1 class="h_bkg">FORM GATE</h1>
		
		
			  
		
	</div>
	

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>