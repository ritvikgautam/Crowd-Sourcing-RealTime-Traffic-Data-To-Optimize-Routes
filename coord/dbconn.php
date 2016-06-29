<?php
	$servername = "localhost";
	$susername = "root";
	$spassword = "123456";
	$dbname = "traffic";
	$conn = mysql_connect($servername, $susername, $spassword);
	mysql_select_db('traffic', $conn);
?>