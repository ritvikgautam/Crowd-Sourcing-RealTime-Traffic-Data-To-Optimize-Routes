<?php
	require ("dbconn.php");

	$sqlStatement = "SELECT * from CurrentRecord;";
	$sqlResult = mysql_query($sqlStatement);

	$arrayJSON = array();
	$finalArrayJSON = array();

	while($sqlRow = mysql_fetch_array($sqlResult, MYSQL_ASSOC))
	{

		
		$OSMID = $sqlRow['osm_id'];
		$slicedArray = array_slice($sqlRow, 1);

		$maxValue = max($slicedArray);
		$maxKey = array_search($maxValue, $slicedArray);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, 'http://open.mapquestapi.com/nominatim/v1/reverse.php?key=ADEMvUnuRMqglTAS8mXry02Vw8z29nWi&format=json&osm_type=W&osm_id='.$OSMID.'');
		$result = curl_exec($ch);
		curl_close($ch);

		
		$DecodeJSONOSMData = json_decode($result);
		$lat = (float)$DecodeJSONOSMData->lat;
		$long = (float)$DecodeJSONOSMData->lon;

		if($maxKey == 'r0')
			$roadSpeed = 3;
		else if($maxKey == 'r1')
			$roadSpeed = 10;
		else if($maxKey == 'r2')
			$roadSpeed = 20;
		else if($maxKey == 'r3')
			$roadSpeed = 30;
		else if($maxKey == 'r4')
			$roadSpeed = 40;
		else if($maxKey == 'r5')
			$roadSpeed = 50;
		else if($maxKey == 'r6')
			$roadSpeed = 60;
		else if($maxKey == 'r7')
			$roadSpeed = 70;
		else if($maxKey == 'r8')
			$roadSpeed = 80;

		$coordinates = [[ $long, $lat ]];
		$arrayJSON["id"] = $OSMID;
		$arrayJSON["points"] = $coordinates;
		$arrayJSON["value"] = $roadSpeed;
		$arrayJSON["value_type"] = "speed";
		$arrayJSON["mode"] = "REPLACE";

		array_push($finalArrayJSON, $arrayJSON);

	}

	$date = new DateTime();
    $day =  $date->format("w");
    if($day == 6 || $day == 0){
      $weekend = 1;
    }else{
      $weekend = 0;
    }

    $time = $date->format("H");

    if($time > 7 && $time <= 10)
      $timeSlot = 2;
    else if($time > 10 && $time <= 16)
      $timeSlot = 3;
    else if($time > 16 && $time <= 20)
      $timeSlot = 4;
    else if($time >20 && $time <=23)
      $timeSlot = 5;
    else {
      $timeSlot = 1;
    }

    $coloumnName = "h" . $weekend . $timeSlot;

	$sqlStatement = 'SELECT osm_id, '.$coloumnName.' FROM HistoricRecord WHERE osm_id NOT IN (SELECT osm_id FROM CurrentRecord)';
	$sqlResult = mysql_query($sqlStatement);

	while($sqlRow = mysql_fetch_array($sqlResult, MYSQL_ASSOC))
	{

		
		$OSMID = $sqlRow['osm_id'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, 'http://open.mapquestapi.com/nominatim/v1/reverse.php?key=ADEMvUnuRMqglTAS8mXry02Vw8z29nWi&format=json&osm_type=W&osm_id='.$OSMID.'');
		$result = curl_exec($ch);
		curl_close($ch);

		
		$DecodeJSONOSMData = json_decode($result);
		$lat = (float)$DecodeJSONOSMData->lat;
		$long = (float)$DecodeJSONOSMData->lon;

		$maxKey = $sqlRow[$coloumnName];

		if($maxKey == 'r0')
			$roadSpeed = 3;
		else if($maxKey == 'r1')
			$roadSpeed = 10;
		else if($maxKey == 'r2')
			$roadSpeed = 20;
		else if($maxKey == 'r3')
			$roadSpeed = 30;
		else if($maxKey == 'r4')
			$roadSpeed = 40;
		else if($maxKey == 'r5')
			$roadSpeed = 50;
		else if($maxKey == 'r6')
			$roadSpeed = 60;
		else if($maxKey == 'r7')
			$roadSpeed = 70;
		else if($maxKey == 'r8')
			$roadSpeed = 80;

		$coordinates = [[ $long, $lat ]];
		$arrayJSON["id"] = $OSMID;
		$arrayJSON["points"] = $coordinates;
		$arrayJSON["value"] = $roadSpeed;
		$arrayJSON["value_type"] = "speed";
		$arrayJSON["mode"] = "REPLACE";

		array_push($finalArrayJSON, $arrayJSON);

	}
	

	$fp = fopen('traffic.json', 'w');
	fwrite($fp, json_encode($finalArrayJSON, JSON_PRETTY_PRINT));
	fclose($fp);
?>