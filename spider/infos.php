<?php 
require 'db.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>煤炭供应信息</title>
</head>
<body>
<?php
$id=$_REQUEST['id'];
if($id == null) exit(0);
$queryTable=$_REQUEST['tb'];
if($queryTable == null) exit(0);

$sql = "select title, content, linkman, address, ip, phone from $queryTable where id=$id";
$query = mysql_query ( $sql );
if (! $query) die ( "没有数据");

while ( $row = mysql_fetch_array ( $query ) ) {
	echo "(title)$row[title](/title)</br>";
	echo "(content)$row[content](/content)</br>";
	echo "(linkman)$row[linkman](/linkman)</br>";
	echo "(address)$row[address](/address)</br>";
	echo "(ip)$row[ip](/ip)</br>";
	echo "(phone)$row[phone](/phone)</br>";
}
?>
</body>
</html>