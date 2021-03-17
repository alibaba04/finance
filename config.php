<?php 
// require_once('./function/mysql.php');
require_once('function/mysql.php');

/*********** Database Settings ***********/
$dbHost = 'localhost';
$dbName = 'u5514609_dbaki'; 


$dbUser = 'u5514609_can';
$dbPass = ',S1s6h8+Mrc)';

$passSalt = 'UFqPNrZENKSQc5yc';

//Default database link
$dbLink = mysql_connect($dbHost,$dbUser,$dbPass, true)or die('Could not connect: ' . mysql_error());
mysql_query("SET NAMES 'UTF8'");

if(!mysql_select_db($dbName,$dbLink))
{
	die('Database Connection Failed!');
}


/*********** Email Settings ***********/
$mailFrom = 'aki';

$mailSupport = 'albaihaqial@gmail.com';

/*********** Display Settings ***********/
$siteTitle = 'Akuntansi AKI';
$recordPerPage = 10;

$wajibIsiKeterangan ='<font style="color:#FF0000; font-weight:bold">Field Bertanda * Wajib Diisi</font>';
$wajibIsiSimbol = '<font style="color:#FF0000; font-weight:bold">&nbsp;&nbsp;*</font>';
$SITUS = "";
?>