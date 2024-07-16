<?php

session_start();
require_once("forumdb_conn.php");
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;



/////////////////////////////////DOWNLOAD FILES////////////////////////////////////////
if(isset($_GET["f"])){
	
	$files="";
	$ctype="";
	$files=$_GET["f"];
	$rdr = $_GET["rdr"];
			
	if(strtolower($rdr)== "profile")
		$folder = "wealth-island-uploads/avatars/";


/////////////////////////////////////////DETERMINE THE CONTENT TYPE/////////////////////////////	
if(file_exists($folder.$files) && is_readable($folder.$files))
	$ctype = mime_content_type($folder.$files);
	
////////////////////////////////////DOWNLOAD THE FILE//////////////////////////////////////////////////////////////	
		
header("content-type:".$ctype);
header("content-disposition: attachment; filename=".$files);
readfile($folder.$files);
}

/////////////////////////////////////////////////////////////////////////////////////
?>

