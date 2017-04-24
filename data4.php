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

$email = $_POST["email"];

$date = new DateTime(); //
$date->setDate($_POST["startYr"], $_POST["startMonth"], $_POST["startDay"]);
$date->setTime($_POST["startHr"], $_POST["startMin"]);
$finalDate = $date->format("Y/m/d/h/i");

//query to get data from the table
$sql = "INSERT INTO ea_wait (time, email) VALUES ('$finalDate', '$email')";

//execute query
$result = $mysqli->query($sql);

//loop through the returned data
echo $_POST["startMin"];


//close connection
$mysqli->close();
?>
