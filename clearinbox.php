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

$not_logged="";

if($_SESSION["username"]){
	
$data="";$tot_row="";
$username=$_SESSION['username'];


///////////PDO QUERY////////////////////////////////////	
	
				$sql = "UPDATE privatemessage SET INBOX='' WHERE USERNAME=?";

				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute(array( $username));
				$tot_row = "(".$stmt1->rowCount().")";

$data= "All ".$tot_row." inbox messages has been cleared successfully <a href='inbox'  class=links >Back to Your Inbox</a>";

}

else{

$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

}


?>


<!DOCTYPE HTML>
<html>
<head>
<title>CLEAR YOUR INBOX</title>
<?php require_once("include-html-headers.php")   ?>

<style>

</style>

</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')    ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	echo "<a href='clearinbox' title=>Clear Your Inbox</a> "  ;
			
	?>
	</header>

	<?php  if($not_logged)  echo "<div class='view_user_wrapper'>".$not_logged."</div>"  ?>

	<?php  if($_SESSION["username"])  {  ?>

	<div class="view_user_wrapper" id="hide_vuwbb">
		<h1 class="h_bkg">CLEAR INBOX</h1>
		<div class="type_b">
			<?php if(isset($data))  echo $data;   ?>
		</div>
	</div>
	<p  hidden id="tempuserholder"></p>
	<?php }  ?>
	<?php require_once('eurofooter.php')    ?>
</div>
</body>
</html>