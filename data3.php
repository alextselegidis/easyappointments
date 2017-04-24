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

//query to get data from the table
$sql = sprintf("SELECT start_datetime, end_datetime FROM ea_appointments");

//execute query
$result = $mysqli->query($sql);

//loop through the returned data
$data = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	array_push($data, $row["start_datetime"]);
}

//now print the data
echo json_encode($data);

//free memory associated with result
$result->close();

//close connection
$mysqli->close();
?>
