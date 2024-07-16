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

$username=$_SESSION['username'];

$checkstatus="checked";



///////////////////////DELETE THE MESSAGE BY SETTING COLUMN TO ""///////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////PDO QUERY////////////////////////////////////	
	
				$sql = "UPDATE privatemessage SET OLD_INBOX='' WHERE  USERNAME=?   AND SELECTION_STATUS=? ";

				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute(array( $username, $checkstatus));
				$totalrows = $stmt1->rowCount();
				

if($totalrows == 1)
	
$data="<span class=black>(<span class=red>".$totalrows."</span>) message has been deleted successfully</span><a class=postusername href='getstdoldinboxdb'> Back to Your Older messages</a>";

else

$data="<span class=black>(<span class=red>".$totalrows."</span>) messages has been deleted successfully </span><a class=postusername href='getstdoldinboxdb'> Back to Your Older messages</a>";


header("location:old-inbox");
exit();




}


else{

$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

}
	

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Old Inbox</title>
<?php require_once("include-html-headers.php")   ?>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php') ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 

	echo "<a href='delete-selected-old-pm' title=>Delete Selected Older Messages</a> "  ;
			
	?>
	</header>

	<?php if($not_logged)   echo "<div class='view_user_wrapper'>".$not_logged."</div>"    ?>

	<?php if($_SESSION["username"]) {  ?>

	<div class="view_user_wrapper">

	<div id="deletedselection"><?php if(isset($data)) echo $data ?></div>

	</div>
	<?php  } ?>
	<?php require_once('eurofooter.php') ?>
</div>
</body>
</html>





