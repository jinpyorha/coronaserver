<?php
//질병목록
//https://ko.wikipedia.org/wiki/%EC%A7%88%EB%B3%91_%EB%AA%A9%EB%A1%9D
//위의 링크에서 data 불러오기
//에러 로그 출력
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

   //db 연결 시작
	$servername = "onesunny3.cafe24.com";
	$username = "onesunny3";
	$password = "gana8338";
	$dbname = "onesunny3";

	// Create connection
	$conn = new mysqli($servername, $username, $password,$dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	//echo "Connected successfully";
	//db 연결 끝
$sql ="SELECT CountryRegion,SUM(Confirmed) AS Confirmed ,SUM(Deaths) AS Deaths ,SUM(Recovered) AS Recovered,DataDate,SUM(NewCases) AS NewCases,SUM(ActiveCases) AS ActiveCases,
SUM(SeriousCritical) AS SeriousCritical,SUM(NewRecovered) AS NewRecovered ,SUM(NewDeaths) AS NewDeaths

FROM CoronaData2 WHERE CountryRegion ='China'
GROUP BY DataDate
ORDER BY DataDate ASC ";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()){
    $sqlsub="INSERT INTO CoronaData2 (ProvinceState,CountryRegion,Confirmed,Deaths,Recovered,WrittenAtUtc,DataDate,NewCases,ActiveCases,SeriousCritical,Pop1M,NewDeaths,NewRecovered)
VALUES ('','China',".$row['Confirmed'].",".$row['Deaths'].",".$row['Recovered'].",NOW(),'".$row['DataDate']."',".$row['NewCases'].",".$row['ActiveCases'].",".$row['SeriousCritical'].",0,".$row['NewDeaths'].",".$row['NewRecovered'].")";

if ($conn->query($sqlsub) === TRUE) {
  echo "New record created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
  }
}



exit;
?>
