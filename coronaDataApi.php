
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
$yesterday = date('Y-m-d',strtotime("-1 days"));
$yesterday2 = date('Y-m-d',strtotime("-2 days"));
// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";
//db 연결 끝

$provinceState = isset($_GET['ProvinceState'])&&$_GET['ProvinceState']!=''?$_GET['ProvinceState']:'';
$countryRegion = isset($_GET['CountryRegion'])&&$_GET['CountryRegion']!=''?$_GET['CountryRegion']:'';
$country=
isset($_GET['country'])&&$_GET['country']!=''?$_GET['country']:'';

//$sqlCountryList ="SELECT ProvinceState,CountryRegion,COUNT(Id)AS cnt FROM CoronaData GROUP BY ProvinceState,CountryRegion";
$sqlCountryList="SELECT ProvinceState,CountryRegion,(SELECT Confirmed FROM CoronaData WHERE ProvinceState=CD.ProvinceState AND CountryRegion=CD.CountryRegion ORDER BY DataDate DESC LIMIT 1 ) AS cnt FROM CoronaData AS CD";
if($country=='US'){
	$sqlCountryList.=" WHERE CD.CountryRegion = '".$country."' AND CD.ProvinceState<>'US'";
}
$sqlCountryList.=" GROUP BY ProvinceState,CountryRegion
ORDER BY cnt DESC ";

if($countryRegion=='US'||$countryRegion=='China'||$countryRegion=='United Kingdom'){
	$sqlCountryData = "SELECT ProvinceState AS PS ,CountryRegion AS CR ,LastUpdate,Confirmed,
	Deaths,Recovered,DataDate AS DD,
	Confirmed-(SELECT Confirmed FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 )  AS Increase,
  Deaths-(SELECT Deaths FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 ) AS DeathsIncrease,
  Recovered-(SELECT Recovered FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 ) AS RecoveredIncrease

	FROM CoronaData ";
}else{
	$sqlCountryData = "SELECT
  ProvinceState AS PS,
  CountryRegion AS CR,
  LastUpdate,
	SUM(Confirmed) AS Confirmed,
  SUM(Deaths) AS Deaths,
  SUM(Recovered) AS Recovered,
  DataDate      AS DD,
  SUM(Confirmed-(SELECT Confirmed FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 )) AS Increase,
  SUM(Deaths-(SELECT Deaths FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 )) AS DeathsIncrease,
  SUM(Recovered-(SELECT Recovered FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 )) AS RecoveredIncrease
	FROM CoronaData ";
}
if($countryRegion=='US'||$countryRegion=='China'){
$sqlCountryDataWhere = "WHERE ProvinceState = '".$provinceState."' AND CountryRegion = '".$countryRegion."' ";
}else{
$sqlCountryDataWhere = "WHERE CountryRegion = '".$countryRegion."' GROUP BY DD ";
}
$sqlCountryDataOrder = "ORDER BY DataDate DESC ";
$sqlCountryData.=$sqlCountryDataWhere.$sqlCountryDataOrder;

//country List 가져오기
$result = $conn->query($sqlCountryList);
if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		//DATA 읽어오기
		//echo '<li>'.$row['ProvinceState'].','.$row['CountryRegion'].','.$row['cnt'].'</li>';
		$countryList[] = array(
			'ProvinceState'=>$row['ProvinceState'],
			'CountryRegion'=>$row['CountryRegion'],
			'cnt'=>$row['cnt']
		);
	}
}

//country Data 가져오기
if(($provinceState!=''||$countryRegion!='')&&($provinceState!='Select States')){

	$result = $conn->query($sqlCountryData);
	$countryDataIndex= 0;
	$recentDate = '';
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			//DATA 읽어오기
			$countryData[] = array(
				'ProvinceState'=>$row['PS'],
				'CountryRegion'=>$row['CR'],
				'LastUpdate'=>$row['LastUpdate'],
				'Confirmed'=>$row['Confirmed'],
				'Active'=>($row['Confirmed']-$row['Deaths']-$row['Recovered']),
				'Deaths'=>$row['Deaths'],
				'Recovered'=>$row['Recovered'],
				'DataDate'=>$row['DD'],
				'Increase'=>$row['Increase']==null?0:(int)$row['Increase'],
				'DeathsIncrease'=>$row['DeathsIncrease']==null?0:(int)$row['DeathsIncrease'],
				'RecoveredIncrease'=>$row['RecoveredIncrease']==null?0:(int)$row['RecoveredIncrease'],
			);

			if($countryDataIndex==0){
				$recentDate=$row['LastUpdate'];
			}
			$countryDataIndex++;
		}
		$countryDataRecent= array(
			'Confirmed'=>$countryData[0]['Confirmed'],
			'Active'=>$countryData[0]['Confirmed']-$countryData[0]['Deaths']-$countryData[0]['Recovered'],
			'Deaths'=>$countryData[0]['Deaths'],
			'Recovered'=>$countryData[0]['Recovered'],
			'DataDate'=>$countryData[0]['DataDate'],
			'Increase'=>$countryData[0]['Increase'],
			'DeathsIncrease'=>$countryData[0]['DeathsIncrease'],
			'RecoveredIncrease'=>$countryData[0]['RecoveredIncrease'],
			//'RecentDate'=>substr($recentDate,0,10)
			'RecentDate'=>$recentDate
		);

	}
}
else{
	$countryData= null;
	//나라 데이터가 없는 경우
	//세계면 나라별 / 미국이면 주별
	//데이터를 모두 sum 해서 recent 값 구한다
	$yesterdaySql="SELECT DataDate FROM CoronaData ORDER BY DataDate DESC LIMIT 1";//최근일
	$yesterdaySql2="SELECT DataDate FROM CoronaData
GROUP BY DataDate
ORDER BY DataDate DESC
LIMIT 1,1";//전일
	$totalRecentDataSql="SELECT
  CountryRegion,
  SUM(Confirmed) AS Confirmed,
  SUM(Deaths)   AS Deaths,
  SUM(Recovered) AS Recovered,
SUM(Confirmed)-(SELECT SUM(Confirmed)  FROM CoronaData WHERE CountryRegion='US' AND DataDate= (".	$yesterdaySql2.")) AS ConfirmedIncrease,
SUM(Deaths)-(SELECT SUM(Deaths)  FROM CoronaData WHERE CountryRegion='US' AND DataDate= (".$yesterdaySql2.")) AS DeathsIncrease,
SUM(Recovered)-(SELECT SUM(Recovered)  FROM CoronaData WHERE CountryRegion='US' AND DataDate= (".	$yesterdaySql2.")) AS RecoveredIncrease,
(".$yesterdaySql.") AS RecentDate
FROM CoronaData
WHERE ";
if($country=='US'){	$totalRecentDataSql.="CountryRegion = 'US' AND ";}
  $totalRecentDataSql.="DataDate =(".$yesterdaySql.")";

	$resultTotal = $conn->query($totalRecentDataSql);

	if ($resultTotal->num_rows > 0) {

		// output data of each row
		while($row = $resultTotal->fetch_assoc()) {
			$countryDataRecent= array(
				'Confirmed'=>$row['Confirmed'],
				'Active'=>$row['Confirmed']-$row['Deaths']-$row['Recovered'],
				'Deaths'=>$row['Deaths'],
				'Recovered'=>$row['Recovered'],
				'Increase'=>$row['ConfirmedIncrease'],
				'DeathsIncrease'=>$row['DeathsIncrease'],
				'RecoveredIncrease'=>$row['RecoveredIncrease'],
				'RecentDate'=>$row['RecentDate']
			);
		}
	}
}
//#######api 제작###########
$coronaArray = array();
$coronaArray['countryData'] = $countryData;
$coronaArray['countryList'] = $countryList;
$coronaArray['countryDataRecent'] = $countryDataRecent;

  $result = "success";
  $reason ="success";
  $loopArray = json_encode($coronaArray);
  jsonMapper($result,$reason,$loopArray);

  //########jsonMapper function###########
  function jsonMapper($result,$reason,$loopArray){

		$loopArray = "
		{
		  \"result\":\"$result\",
		  \"reason\":\"$reason\",
		  \"data\":$loopArray
		 }
		";

		echo $loopArray;
	}


	 /*dataYn  : Y, N 추가*/
	function jsonDataMapper($result,$reason,$loopArray, $dataYn){

		$loopArray = "
		{
		  \"result\":\"$result\",
		  \"reason\":\"$reason\",
		  \"dataYn\":\"$dataYn\",
		  \"data\":$loopArray
		 }
		";

		echo $loopArray;
	}


?>
