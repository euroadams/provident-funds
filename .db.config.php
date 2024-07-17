<?php

define("IS_DEV", (isset($_SERVER[$K="HTTP_HOST"]) && preg_match('#\.test$#', $_SERVER[$K])));


//$pdo_conn = new PDO("mysql:host=sql309.phpnet.us;dbname=pn_19234541_eurotech", "pn_19234541", "adimabua02");//phpnet.us///
//$pdo_conn = new PDO("mysql:host=sql100.phpnet.us;dbname=pn_19225500_eurotech", "pn_19225500", "adimabua02");//phpnet.us///

//("mysql:host=provident-funds.test;dbname=wealth", "root", "");  ///localhost///
//("mysql:host=localhost;dbname=a7499969_login", "root", "");

//("mysql:host=localhost;dbname=provid33_wealth", "provid33_pwealth", "PFwealth+P&A_20PFin90");///6te.net/////



$dbName = IS_DEV? 'wealth' : 'provid33_wealth';
$dbHost = IS_DEV? 'provident-funds.test' : 'localhost';
$dbUsername = IS_DEV? 'root' : 'provid33_pwealth';
$dbPassword = IS_DEV? '' : 'PFwealth+P&A_20PFin90';

define("DB_NAME", $dbName);
define("DB_HOST", $dbHost);
define("DB_USERNAME", $dbUsername);
define("DB_PWD", $dbPassword);



?>