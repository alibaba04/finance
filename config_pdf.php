<?php 
require_once('function/mysql.php');

/*********** Database Settings ***********/
$dbHost = 'localhost';
$dbName = 'u5514609_dbaki'; 

$dbUser = 'u5514609_can';
$dbPass = ',S1s6h8+Mrc)';

$passSalt = 'UFqPNrZENKSQc5yc';

//Default database link.
$dbLink = mysqli_connect($dbHost,$dbUser,$dbPass,$dbName)or die('Could not connect: ' . mysql_error());

?>
