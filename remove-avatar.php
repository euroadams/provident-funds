<?php  
session_start();
require_once("phpfunctions.php");

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

$username = $_SESSION["username"];

if($username){

	if(isset($_POST["file"]) ||  isset($_GET["file"])){
		
		
		if(isset($_POST["file"]))
			$file_passed = $_POST["file"];

		if(isset($_GET["file"]))
			$file_passed = $_GET["file"];
				
		//////////////////UPDATE THE USER DB//////////////////////////////////				
			
			
		///////////PDO QUERY////////////////////////////////////
					
						$sql = "UPDATE  members   SET AVATAR='' WHERE USERNAME=? LIMIT 1";

						$stmt1 = $pdo_conn_login->prepare($sql);
						if($stmt1->execute(array($username))){
							
							
		///////////////DELETE THE FILE FROM SERVER//////////////////////////////////				
						
						$path2del = "wealth-island-uploads/avatars/".$file_passed;
						
						if(realpath($path2del) && $file_passed)
							unlink($path2del);
						
						}
							
			
		
	}

}
if(isset($_GET["file"])){
	
	header("location:edit-profile");
	exit();
}


?>