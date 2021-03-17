<?php 
require_once('function/mysql.php');

/*********** Database Settings ***********/
$dbHost = 'localhost';
$dbName = 'u8364183_finance'; 


$dbUser = 'u8364183_marketing';
$dbPass = 'PVMMA0Akp4;(';

$passSalt = 'UFqPNrZENKSQc5yc';

//Default database link.
$dbLink = mysqli_connect($dbHost,$dbUser,$dbPass,$dbName)or die('Could not connect: ' . mysql_error());

?>
