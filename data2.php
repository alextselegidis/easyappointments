<?php
//setting header to json
//header('Content-Type: application/json');

//database
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'easyappointmentsdb');

//get connection
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
	die("Connection failed: " . $mysqli->error);
}



$todayDay = $_POST["todayDay"];

$date = new DateTime(); //
$date->setDate($_POST["startY"], $_POST["startM"] + 1, $_POST["startD"] + $todayDay);
$startDate = $date->format("Y/m/d");

$date->setDate($_POST["startY"], $_POST["startM"] + 1, $_POST["startD"] + $todayDay + 1);
$endDate = $date->format("Y/m/d");

//query to get data from the table
$sql = sprintf("SELECT start_datetime, end_datetime FROM ea_appointments WHERE start_datetime BETWEEN '$startDate' AND '$endDate'");

//execute query
$result = $mysqli->query($sql);

//loop through the returned data
$data = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$rowdata = array(0 => $row["start_datetime"], 1 => $row["end_datetime"]);
	array_push($data, $rowdata);
}

//now print the data
echo json_encode($data);

//free memory associated with result
$result->close();

//close connection
$mysqli->close();
?>
