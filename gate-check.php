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

if($username){

	$logged = '<div class="errors">YOU ARE LOGGED<br/>REDIRECTING YOU, PLEASE WAIT......<div class="show_rdrt"></div></div>';


}
else{
	
	$not_logged = '<div class="errors">YOU ARE NOT LOGGED<br/>REDIRECTING YOU, PLEASE WAIT.....<div class="show_rdrt"></div></div>';
}



if(isset($logged)){
	
	$rdr_to = 'dash-board';

}
elseif(isset($not_logged)){
	
	$rdr_to = 'register';
	
}else{
	
	$rdr_to = $getdomain;			
	
}
 
 ?>
 
 
 
<!DOCTYPE HTML>
<html>
<head>
<title>GATE CHECK</title>
<?php 
			
	echo '<meta http-equiv="refresh" content="5; url='.$rdr_to.'" />';
	
	require_once("include-html-headers.php");

	$rdr_t = (time() + 5);
	
	echo '<script>startRdrCountDown('.$rdr_t.')</script>';


 ?>
</head>
<body>
<div class="wrapper">
	<?php //require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	$page_self = getReferringPage("qstr url");

	echo "<a href='".$page_self."' title=>Gate Check</a> "  ;
			
	?>
	</header>
		
	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<?php getMidPageScroll(); ?>

		<h1 class="h_bkg">GATE CHECK</h1>
		
		<?php 
		if(isset($logged)){
			
			header("refresh:5; url=dash-board");
			echo $logged; 
		
		}
		elseif(isset($not_logged)){
			
			header("refresh:5; url=register");
			echo $not_logged; 			
			
		}else{
			
			header("refresh:5; url=".$getdomain);			
			echo '<div class="errors">REDIRECTING YOU....<div class="show_rdrt"></div></div>';			
			
		}
		?>
		
		
			  
		
	</div>
	

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>