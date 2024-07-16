<?php


session_start();
require_once("forumdb_conn.php");
require_once("phpfunctions.php");

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;


if(isset($_POST["message_id"])){
	
$data="";$checkstatus="";$row="";$checked="";$message="";
$username=$_SESSION['username'];

$message_id=$_POST["message_id"];


////////////////////////////////////////GET CHECK_STATUS FROM DB/////////////////////////////////////////////////////////////////////////////////////////

///////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT SELECTION_STATUS FROM privatemessage  WHERE USERNAME LIKE ?  AND ID =? LIMIT 1";
		
				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute(array($username, $message_id));
	

$row = $stmt1->fetch(PDO::FETCH_ASSOC);

$checkstatus=$row['SELECTION_STATUS'];

///////////////////////////IF CHECK_STATUS IS "" OR UNCHECKED THEN CHECK IT/////////////////////////////////////////////////////////////////////////////////////////////////

if($checkstatus=="" || $checkstatus=="unchecked")
$checkstatus="checked";


/////////////////ELSE IF CHECK_STATUS IS CHECKED UNCHECK IT////////////////////////////////////////////////////////////////////////////////////////////////////////////

else if($checkstatus=="checked")
	$checkstatus="unchecked";



///////EXECUTE THE CHECKING ACCORDINGLY IN THE DB////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////PDO QUERY////////////////////////////////////	
	
				$sql = "UPDATE privatemessage SET  SELECTION_STATUS=? WHERE USERNAME LIKE ?  AND ID= ?";
		
				$stmt2 = $pdo_conn_login->prepare($sql);
				$stmt2->execute(array($checkstatus, $username, $message_id));	


///////////////////GET TOTAL NUMBER OF MESSAGES CHECKED FOR DELETE///////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT * FROM privatemessage  WHERE USERNAME LIKE ? AND SELECTION_STATUS='checked' ";
		
				$stmt3 = $pdo_conn_login->prepare($sql);
				$stmt3->execute(array($username));
	
$row = $stmt3->rowCount();



if($row == 1)
$message="<span class=black>(<span class=red>".$row."</span>) message checked for delete</span>";

else if($row > 1)
$message="<span class=black>(<span class=red>".$row."</span>) messages checked for delete</span>";

else if($row < 1)
	$message = "";

$data = "<b><div class='red'>".$message."</div><b/>";

echo $data;

}
?>