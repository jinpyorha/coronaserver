
<?php
//db 연결 시작
$servername = "onesunny3.cafe24.com";
$username = "onesunny3";
$password = "gana8338";
$dbname = "onesunny3";

$conn = new mysqli($servername, $username, $password,$dbname);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$yesterday = date('Y-m-d',strtotime("-1 days"));
$dataDate=$yesterday;
$sqlWorldRank = "SELECT
  ProvinceState AS PS,
  CountryRegion AS CR,
  LastUpdate,
  SUM(Confirmed) AS Confirmed,
  SUM(Deaths)   AS Deaths,
  SUM(Recovered) AS Recovered,
  DataDate      AS DD,
  SUM(Confirmed-(SELECT Confirmed FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 )) AS Increase,
  SUM(Deaths-(SELECT Deaths FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 )) AS DeathsIncrease,
  SUM(Recovered-(SELECT Recovered FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 )) AS RecoveredIncrease
FROM CoronaData
WHERE DataDate  = '".$dataDate."'
GROUP BY DD,CountryRegion
ORDER BY Increase DESC
LIMIT 10";
$result = $conn->query($sqlWorldRank);
$rankIndex=1;
if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
    $sql="INSERT INTO CoronaDataRank (CountryRegion,Confirmed,Deaths,Recovered,DataDate,Increase,DeathsIncrease,RecoveredIncrease,Rank) VALUES ('".$row['CR']."',".$row['Confirmed'].",".$row['Deaths'].",".$row['Recovered'].",'".$dataDate."',".$row['Increase'].",".$row['DeathsIncrease'].",".$row['RecoveredIncrease'].",".$rankIndex."
    )";
    if ($conn->query($sql) === TRUE) {
      //echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    echo $sql;
    $rankIndex++;
  }
}
