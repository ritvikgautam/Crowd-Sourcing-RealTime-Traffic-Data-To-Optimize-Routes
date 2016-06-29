<?php
	
	//Import Database Information

	require("dbconn.php");


	//Import Information from the Client

	$speed = $_POST['speed'];
	$geohash = $_POST['geohash'];
	$geohash = substr($geo, 0, -3);
	$geohashurl = $geohash."/text";
	$coordinates = file_get_contents($geohashurl);
	$arraycoordinates = explode(" ", $coordinates);
	$lat = $arraycoordinates[0];
	$long = $arraycoordinates[1];
	
	if($speed != 0)
	{

		//Get the OSM ID
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, 'http://open.mapquestapi.com/nominatim/v1/reverse.php?key=ADEMvUnuRMqglTAS8mXry02Vw8z29nWi&format=json&lat='.$lat.'&lon='.$long.'');
		$result = curl_exec($ch);
		curl_close($ch);

		$DecodeJSONOSMData = json_decode($result);
		$OSMID = $DecodeJSONOSMData->osm_id;

		
		// Calculate the Speed Group

		// To understand this expression, check Database table names
		//$SpeedGroup = ($speed / 5) - ((($speed / 5) + 1) % 2) - 1;

		// Change this method
		if($speed <= 5)
			$SpeedGroup = 0;
		else if($speed > 5 && $speed <= 15)
			$SpeedGroup = 1;
		else if($speed > 15 && $speed <= 25)
			$SpeedGroup = 2;
		else if($speed > 25 && $speed <= 35)
			$SpeedGroup = 3;
		else if($speed > 35 && $speed <= 45)
			$SpeedGroup = 4;
		else if($speed > 45 && $speed <= 55)
			$SpeedGroup = 5;
		else if($speed > 55 && $speed <= 65)
			$SpeedGroup = 6;
		else if($speed > 65 && $speed <= 75)
			$SpeedGroup = 7;
		else if($speed > 75)
			$SpeedGroup = 8;

		//Update the counter in the database
		$sqlStatement = "INSERT INTO CurrentRecord(osm_id, r".$SpeedGroup.") VALUES(".$OSMID.",0);";
		$sqlResult = mysql_query($sqlStatement);

		$sqlStatement = "UPDATE CurrentRecord set r".$SpeedGroup." = r".$SpeedGroup." + 1 WHERE osm_id = ".$OSMID.";";
		$sqlResult = mysql_query($sqlStatement);
	}

?>
