<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>煤炭求购信息</title>
</head>
<body>
<ul>
  
<?php
require 'db.php';
$queryTable = "spider_coalbuy";
//$inf="http://www.xbmtxx.com/spider/infos.php";
$inf="http://www.mtxxlm.net/spider/infos.php";

$sql = "select id from $queryTable";
$query = mysql_query ( $sql );
if (! $query) die ( "没有数据");

while ( $row = mysql_fetch_array ( $query ) ) {
	echo "<li><a href='$inf?id=$row[id]&tb=spider_coalbuy'>$row[id]</a></li>";
}
?>

</ul>
</body>
</html>