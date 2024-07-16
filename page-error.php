<?php

session_start();
require_once("forumdb_conn.php");
require_once("phpfunctions.php");


///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();
date_default_timezone_set("Africa/Lagos");



?>



<!DOCTYPE HTML>

<html>
<head>
<title>Error 404: Page Not Found </title>

<?php require_once("include-html-headers.php")   ?>

<style>

</style>

</head>
<body >

<div class="wrapper" id="go_up">

	<?php require_once('euromenunav.php')   ?>


	<header class="mainnav">
	<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

	<?php 
		echo "<a href='page-error'>Page Error</a>";
			
	?>
	</header>	

	<!--<div class="postul">(<a class="links" href="#go_down">Go Down</a>)</div>-->
	<div class="view_user_wrapper">
	
	<h1>Sorry the Page You Requested was Not Found !<br/><span class="red">(ERROR 404)</span></h1>
	
	</div>

	<!--<div class="postul">(<a class="links" href="#go_up">Go Up</a>)</div>
	
	<span id="go_down"></span>-->
	
	<?php require_once('eurofooter.php')   ?>
</div>
</body>
</html>

