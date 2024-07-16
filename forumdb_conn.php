<?php 

///////IMPORTANT/////////////////


if(!isset($_SESSION["username"]))
	$_SESSION["username"] = "";

/*
if(isset($_COOKIE["username"])  && $_SESSION["username"] == ""){
	
	if($_COOKIE["username"])
		$_SESSION["username"] = $_COOKIE["username"];
}
*/

/*
$dbname="eurotech_forum";
$dbusername="root";//"a7499969_adims";
$dbpass="";//"adimabua02";
$dbhost="localhost";//"mysql11.000webhost.com";


//////////////////////////EUROTECH_FORUM DB////////////////////////////////////////////////////////////////////////////


///////////////MYSQLI PROCEDURAL CONNECTION/////////////////////////////////////////////////////////////////////////////////////////////			
			
			$mysqli_conn = mysqli_connect($dbhost, $dbusername, $dbpass, $dbname)
			or die("unable to connect to the database ".mysqli_connect_error());

////////////////////MYSQLI OO CONNNECTION/////////////////////////////////////////////////////////////////////////////////

			$mysqli_oo_conn = new mysqli($dbhost, $dbusername, $dbpass, $dbname)

			or die("unable to connect to the database ".mysqli_connect_error());



/////////////////EUROTECH DB///////////////////////////////////////////////////////////////////////////////////////////////
			
$dbname2="eurotech";
$dbusername2="root";//"a7499969_adims";
$dbpass2="";//"adimabua02";
$dbhost2="localhost";//"mysql11.000webhost.com";


///////////////MYSQLI PROCEDURAL CONNECTION/////////////////////////////////////////////////////////////////////////////////////////////			
			
			$mysqli_conn2 = mysqli_connect($dbhost2, $dbusername2, $dbpass2, $dbname2)
			or die("unable to connect to the database ".mysqli_connect_error());

////////////////////MYSQLI OO CONNNECTION/////////////////////////////////////////////////////////////////////////////////

			$mysqli_oo_conn2 = new mysqli($dbhost2, $dbusername2, $dbpass2, $dbname2)

			or die("unable to connect to the database ".mysqli_connect_error());


/////////////LOGIN DB/////////////////////////////////////////////////////////////////////////////////////////////////////			
			
			
			
			
			
/////////////LOGIN DB/////////////////////////////////////////////////////////////////////////////////////////////////////			
			
			
				
$dbname_login="a7499969_login";
$dbusername_login="root";//"a7499969_adims";
$dbpass_login="";//"adimabua02";
$table_login="chatmessage";
$dbhost_login="localhost";//"mysql11.000webhost.com";	
		
///////////////MYSQLI PROCEDURAL CONNECTION/////////////////////////////////////////////////////////////////////////////////////////////			
			
$mysqli_conn_login = mysqli_connect($dbhost_login, $dbusername_login, $dbpass_login, $dbname_login)
			or die("unable to connect to the database ".mysqli_connect_error());

////////////////////MYSQLI OO CONNNECTION/////////////////////////////////////////////////////////////////////////////////

	$mysqli_oo_conn_login = new mysqli($dbhost_login, $dbusername_login, $dbpass_login, $dbname_login)

			or die("unable to connect to the database ".mysqli_connect_error());

			
		
			
////////////////PDO CONNECTION///////////////////////////////////////////////////////////
			
	try{
		
		$pdo_conn = new PDO("mysql:host=localhost;dbname=eurotech", "root", "");
		$pdo_conn_login = new PDO("mysql:host=localhost;dbname=a7499969_login", "root", "");
		
		$pdo_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	}

	catch(PDOException $e){
		
		echo $e->getMessage();
		
		
		
	}*/

?>