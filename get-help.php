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

<title>Get Help</title>
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

		echo "<a href='get-help' title=>Get Help</a> "  ;
				
		?>
	</header>

	<div class="view_user_wrapper">
		
	</div>
	<?php require_once('eurofooter.php')     ?>
</div>
</body>

</html>


