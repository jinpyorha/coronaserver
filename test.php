<?php

$servername = 'corona.cdvmwkpszam8.us-east-2.rds.amazonaws.com';
$username = 'root';
$password = 'samsamsam';
$dbname = 'corona';

	$conn = new mysqli($servername, $username, $password,$dbname);

if ($conn->connect_error) {
	die("Connection failed: ".$conn->connect_error);
}

$sql = array();
$sql[0]="SELECT * FROM practice"; $result = $conn->query($sql[0]);
if ($result->num_rows > 0) {
//output data of each row
while($row = $result->fetch_assoc()) {
echo '<div>'.$row['name'].','.$row['birth'].'</div>'; }
}



?>
