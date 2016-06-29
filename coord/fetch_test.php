<?php
	if(isset($_POST['send_fetch_test']))
	{
		$speed = $POST['speed'];
		$lat = $_POST['lat'];
		$long = $_POST['long'];
		$geohash = file_get_contents("http://geohash.org/?q=$lat,$long&format=url&redirect=0");
	}
?>
<html>
	<head>
		<title> Test Page for sending dummy POST messages! </title>
	</head>
	<body>
			<form action="fetch.json" method="post">
				<label> Speed:
				<input name="speed"><br>
				<label> Lat:
				<input name="lat"><br>
				<label> Long:
				<input name="long"><br>
				<label> Geohash:
				<input name="geohash"><br>
				<input type="submit" name="send_fetch_test">
			</form>
	</body>
</html>