
<?php
//db 연결 시작
$servername = "onesunny3.cafe24.com";
$username = "onesunny3";
$password = "gana8338";
$dbname = "onesunny3";
/*
$servername = 'corona.cdvmwkpszam8.us-east-2.rds.amazonaws.com';
$username = 'root';
$password = 'samsamsam';
$dbname = 'corona';
*/
// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";
//db 연결 끝

$sql="SELECT
  Id,
  ProvinceState AS PS,
  CountryRegion AS CR,
  LastUpdate,
  Confirmed,
  Deaths,
  Recovered,
  DataDate      AS DD,
  Confirmed-(SELECT Confirmed FROM CoronaData2 WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 ) AS Increase,
  Deaths-(SELECT Deaths FROM CoronaData2 WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 ) AS DeathsIncrease,
  Recovered-(SELECT Recovered FROM CoronaData2 WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 ) AS RecoveredIncrease,
  (SELECT DataDate FROM CoronaData2 WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 ) AS yesterday
FROM CoronaData2";
$sql.=" WHERE DataDate > '2020-03-15' AND DataDate <='2020-03-23'";
    //AND CountryRegion = 'S. Korea'
//$sql.=" AND ProvinceState = ''";

    echo $sql.'<br><br>';

$result = $conn->query($sql);
if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		//DATA 읽어오기
		//echo '<li>'.$row['ProvinceState'].','.$row['CountryRegion'].','.$row['cnt'].'</li>';
		$countryList[] = array(
      'Id'=>$row['Id'],
			'ProvinceState'=>$row['PS'],
			'CountryRegion'=>$row['CR'],
			'cnt'=>$row['cnt'],
      'Confirmed'=>$row['Confirmed'],
      'Deaths'=>$row['Deaths'],
      'Recovered'=>$row['Recovered'],
      'DataDate'=>$row['DD'],
      'Increase'=>$row['Increase'],
      'DeathIncrease'=>$row['DeathsIncrease'],
      'RecoveredIncrease'=>$row['RecoveredIncrease']
		);

    $increase = $row['Increase']!=null?$row['Increase']:0;
    $active =($row['Confirmed']-$row['Deaths']-$row['Recovered']);
    $deathsIncrease = $row['DeathsIncrease']!=null?$row['DeathsIncrease']:0;
    $recoveredIncrease = $row['RecoveredIncrease']!=null?$row['RecoveredIncrease']:0;



    $updateSql ="
    UPDATE CoronaData2 SET NewCases=".$increase.",ActiveCases=".$active.",NewDeaths=".$deathsIncrease.",NewRecovered= ".$recoveredIncrease ."
    WHERE Id =".$row['Id'];
    //echo $updateSql.'<br>';

    if ($conn->query($updateSql) === TRUE) {
      //echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

	}
}
//var_dump($countryList);


?>
