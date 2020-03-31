<?php
/*이 파일은 가장 최신에 insert한 일자의 data중에서
confirmed,recovered,deaths 의 increase 값이
0으로 들어온게 있는지, 제대로 된 데이터 인지 검사 후
계산값과 비교하여 다르다면 계산된 값으로 update 해주는 파일읿니다.
datainsert (ctounry,usa) 후 , 2분뒤 매시간 자동으로 실행되고 있습니다.
*/
$yesterday = date('Y-m-d',strtotime("-1 days"));
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


  $sql = "SELECT * FROM
(SELECT
  Id,
  CountryRegion AS CR,
  ProvinceState AS PS,
  DataDate AS DD,
  Confirmed,
  NewCases,
  Confirmed-(SELECT Confirmed FROM CoronaData2 WHERE CountryRegion=CR AND ProvinceState = PS AND DataDate < DD ORDER BY DataDate DESC LIMIT 1) AS calConfirmed,
  Deaths,
  NewDeaths,
  Deaths-(SELECT Deaths FROM CoronaData2 WHERE CountryRegion=CR AND ProvinceState = PS AND DataDate < DD ORDER BY DataDate DESC LIMIT 1) AS calDeaths,
  Recovered,
  NewRecovered,
  Recovered-(SELECT Recovered FROM CoronaData2 WHERE CountryRegion=CR AND ProvinceState = PS AND DataDate < DD ORDER BY DataDate DESC LIMIT 1) AS calRecovered,
  ActiveCases
FROM CoronaData2
WHERE (NewCases = 0
        OR NewDeaths = 0
        OR NewRecovered = 0)
        AND DataDate>'".$yesterday."'
ORDER BY DD DESC
LIMIT 1000
)
AS a
WHERE

        (calConfirmed!=NewCases AND calConfirmed>0)
        OR
        (calDeaths !=NewDeaths AND calDeaths>0)
        OR
        (calRecovered!=NewRecovered AND calRecovered>0)";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
      $tempNewCases=0;
      $tempNewRecovered=0;
      $tempNewDeaths=0;
      $tempId = $row['Id'];
      $tempState = 0;
      $sqlRun="";

      //echo $row['CountryRegion'].','.$row['ProvinceState'].','.$row['DataDate'].','.$row['Confirmed'].'<br>';
      $yesterday = date('Y-m-d',strtotime($row['DD']." -1 days"));
      $subSql = "
SELECT Id,CountryRegion,ProvinceState,Confirmed,NewCases,Deaths,NewDeaths,Recovered,NewRecovered,ActiveCases,DataDate FROM CoronaData2
WHERE CountryRegion='".$row['CR']."' AND ProvinceState='".$row['PS']."' AND DataDate ='".$yesterday."'
ORDER BY DataDate DESC LIMIT 1";
      $resultSub = $conn->query($subSql);
      if($resultSub->num_rows>0){
        $subrow=$resultSub->fetch_assoc();
        //echo $subrow['CountryRegion'].','.$subrow['ProvinceState'].','.$subrow['DataDate'].','.$subrow['Confirmed'].'<br><br>';

        $tempNewCases=$row['Confirmed']-$subrow['Confirmed'];
        $tempNewRecovered=$row['Recovered']-$subrow['Recovered'];
        $tempNewDeaths=$row['Deaths']-$subrow['Deaths'];

        if($row['NewCases']!=$tempNewCases){
          $tempState=1;
          echo 'NewCases : '.$row['NewCases'].'->'.$tempNewCases.'<br>';
          $sqlRun ="UPDATE CoronaData2 SET NewCases= ".$tempNewCases." WHERE Id = ".$tempId;
          if($conn->query($sqlRun)){echo 'success';}
          echo $sqlRun.'<br>';
        }
        if($row['NewRecovered']!=$tempNewRecovered){
          $tempState=1;
          echo 'NewRecovered : '.$row['NewRecovered'].'->'.$tempNewRecovered.'<br>';
          $sqlRun ="UPDATE CoronaData2 SET NewRecovered= ".$tempNewRecovered." WHERE Id = ".$tempId;
          if($conn->query($sqlRun)){echo 'success';}
          echo $sqlRun.'<br>';
        }
        if($row['NewDeaths']!=$tempNewDeaths){
          $tempState=1;
          echo 'NewDeaths : '.$row['NewDeaths'].'->'.$tempNewDeaths.'<br>';
          $sqlRun ="UPDATE CoronaData2 SET NewDeaths= ".$tempNewDeaths." WHERE Id = ".$tempId;
          if($conn->query($sqlRun)){echo 'success';}
          echo $sqlRun.'<br>';
        }
        if($tempState>0){
          echo '[origin]'.$row['CountryRegion'].','.$row['ProvinceState'].','.$row['DataDate'].','.$row['Confirmed'].','.$row['NewCases'].','.$row['Deaths'].','.$row['NewDeaths'].','.$row['Recovered'].','.$row['Deaths'].','.$row['NewDeaths'].'<br>';
          echo '[yesterday]'.$subrow['CountryRegion'].','.$subrow['ProvinceState'].','.$subrow['DataDate'].','.$subrow['Confirmed'].','.$subrow['NewCases'].','.$subrow['Deaths'].','.$subrow['NewDeaths'].','.$subrow['Recovered'].','.$subrow['Deaths'].','.$subrow['NewDeaths'].'<br>########################<br><br>';
        }
      }

    }
  }
?>
