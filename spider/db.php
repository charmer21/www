<?php 
//$host = "localhost:3306";
//$user = "root";
//$pwd = "";
//$database = "spider";

$host = "localhost:3306";
$user = "s508411db0";
$pwd = "c409615998";
$database = "s508411db0";

$con = mysql_connect ( $host, $user, $pwd );
if (! $con) {
	die ( 'Could not connect: ' . mysql_error () );
}
mysql_query ( "set names 'utf8'" ); //编码转化 
$db_selecct = mysql_select_db ( $database ); //选择数据库 
if (! $db_selecct) {
	die ( "could not to the database</br>" . mysql_error () );
}
?>