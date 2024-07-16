<?php  
session_start();

session_unset();

session_destroy();

//setcookie("username", "", (time() - 1814400));

header('location:login');
exit();
	


?>