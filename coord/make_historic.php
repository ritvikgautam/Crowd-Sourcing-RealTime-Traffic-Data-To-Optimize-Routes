<?php

  //Establist a database connection
  require("dbconn.php");

  //Enable for Error Reporting in the page
  /*
  error_reporting(E_ALL);
  ini_set('display_errors',1);
  */

  //Fetch values from the CurrentRecord table
  $sqlStatement = "SELECT * from CurrentRecord;";
	$sqlResult = mysql_query($sqlStatement);

  //Get current Date and Time
  $date = new DateTime();

  //For each entry in the CurrentRecord
	while($sqlRow = mysql_fetch_assoc($sqlResult))
	{

    //Get OSMID and the speed category for each row
    $OSMID = $sqlRow['osm_id'];
    $slicedArray = array_slice($sqlRow, 1);

		$maxValue = max($slicedArray);
		$maxKey = array_search($maxValue, $slicedArray);

    //Check if today is a weekday or weekend
    $day =  $date->format("w");
    if($day == 6 || $day == 0){
      $weekend = 1;
    }else{
      $weekend = 0;
    }

    //Give timeslot to current time to store in HistoricRecord
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

    /* Historic tables are named as follows"
    * Historic[weekday or weekend][time slot]
    *
    * Weekday -> 0 ; Weekend -> 1
    *
    * Time slots
    * 23:00 - 07:00 = 1
    * 07:00 - 10:00 = 2
    * 10:00 - 16:00 = 3
    * 16:00 - 20:00 = 4
    * 20:00 - 23:00 = 5
    */

    $tableName = "Historic" . $weekend . $timeSlot;

    $sqlStatement = "INSERT INTO ".$tableName."(osm_id, ".$maxKey.") VALUES(".$OSMID.",0);";
  	$sqlResultNew = mysql_query($sqlStatement);

  	$sqlStatement = "UPDATE ".$tableName." set ".$maxKey." = ".$maxKey." + 1 WHERE osm_id = ".$OSMID.";";
  	$sqlResultNew = mysql_query($sqlStatement);

  }

//Store value in HistoricRecord
  for($i = 0; $i <= 1; $i++)
  {
    for($j = 1; $j <= 5; $j++)
    {
      $tableName = "Historic" . $i . $j;
      $sqlStatement = "SELECT * from ".$tableName.";";
    	$sqlResult = mysql_query($sqlStatement);

      $coloumnName = "h" . $i . $j;

      while($sqlRow = mysql_fetch_assoc($sqlResult))
      {
        $OSMID = $sqlRow['osm_id'];
        $slicedArray = array_slice($sqlRow, 1);

    		$maxValue = max($slicedArray);
    		$maxKey = array_search($maxValue, $slicedArray);

        $sqlStatement = "INSERT INTO HistoricRecord (osm_id, ".$coloumnName.") VALUES(".$OSMID.",0);";
        $sqlResultNew = mysql_query($sqlStatement);

        $sqlStatement = "UPDATE HistoricRecord set ".$coloumnName." = '".$maxKey."' WHERE osm_id = ".$OSMID.";";
        $sqlResultNew = mysql_query($sqlStatement);
      }
    }
  }

  //Truncate value from the CurrentRecord table
  $sqlStatement = "TRUNCATE table CurrentRecord;";
  $sqlResultNew = mysql_query($sqlStatement);
?>
