
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
$today = date('Y-m-d');
$yesterday = date('Y-m-d',strtotime("-1 days"));
$yesterday2 = date('Y-m-d',strtotime("-2 days"));

$yesterdaySql="SELECT DataDate FROM CoronaData2 ORDER BY DataDate DESC LIMIT 1";//최근일
$yesterdaySql2="SELECT DataDate FROM CoronaData2
GROUP BY DataDate
ORDER BY DataDate DESC
LIMIT 1,1";//전일

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

//$sqlCountryList ="SELECT ProvinceState,CountryRegion,COUNT(Id)AS cnt FROM CoronaData2 GROUP BY ProvinceState,CountryRegion";
$sqlCountryList="SELECT ProvinceState,CountryRegion,(SELECT Confirmed FROM CoronaData2 WHERE ProvinceState=CD.ProvinceState AND CountryRegion=CD.CountryRegion ORDER BY DataDate DESC LIMIT 1 ) AS cnt FROM CoronaData2 AS CD";
$sqlCountryList.=" WHERE CD.ProvinceState<>'Total:' AND CD.CountryRegion<>'Total:'";
if($country=='USA'){
	$sqlCountryList.=" AND CD.CountryRegion = '".$country."' AND CD.ProvinceState<>''";
}

$sqlCountryList.="  GROUP BY ProvinceState,CountryRegion
ORDER BY cnt DESC ";

	$sqlCountryData = "SELECT ProvinceState AS PS ,CountryRegion AS CR ,LastUpdate,Confirmed,
	Deaths,Recovered,DataDate AS DD,
	NewCases ,
 	NewDeaths AS DeathsIncrease,
	ActiveCases,
  NewRecovered AS RecoveredIncrease,";

	if($countryRegion=='USA'){
		$sqlCountryDataWhere = " WHERE ProvinceState = '".$provinceState."' AND CountryRegion = '".$countryRegion."' AND ProvinceState<>'Total:' AND CountryRegion<>'Total:' AND ProvinceState<>'' ";
	}else{
	$sqlCountryDataWhere = " WHERE CountryRegion = '".$countryRegion."' AND ProvinceState<>'Total:' AND CountryRegion<> 'Total:'";
	}

	$sqlCountryData.="ActiveCases-(SELECT ActiveCases FROM CoronaData2 ".$sqlCountryDataWhere." AND DataDate=(".$yesterdaySql2.")
	  )AS ActiveCasesIncrease ";

	$sqlCountryData .= "FROM CoronaData2 ";

$sqlCountryDataOrder = " ORDER BY DataDate DESC ";
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
if(($provinceState!=''||$countryRegion!='')&&($provinceState!='Select States'&&$provinceState!='Select country')){
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
				'Active'=>(int)$row['ActiveCases'],
				'Deaths'=>$row['Deaths'],
				'Recovered'=>$row['Recovered'],
				'DataDate'=>$row['DD'],
				'Increase'=>(int)$row['NewCases'],
				'DeathsIncrease'=>$row['DeathsIncrease']==null?0:(int)$row['DeathsIncrease'],
				'RecoveredIncrease'=>$row['RecoveredIncrease']==null?0:(int)$row['RecoveredIncrease'],
				'ActiveIncrease'=>$row['ActiveCasesIncrease']==null?0:(int)$row['ActiveCasesIncrease'],
			);

			if($countryDataIndex==0){
				$recentDate=$row['LastUpdate'];
			}
			$countryDataIndex++;
		}
		$countryDataRecent= array(
			'Confirmed'=>$countryData[0]['Confirmed'],
			'Active'=>$countryData[0]['Active'],
			'Deaths'=>$countryData[0]['Deaths'],
			'Recovered'=>$countryData[0]['Recovered'],
			'DataDate'=>$countryData[0]['DataDate'],
			'Increase'=>$countryData[0]['Increase'],
			'DeathsIncrease'=>$countryData[0]['DeathsIncrease'],
			'RecoveredIncrease'=>$countryData[0]['RecoveredIncrease'],
			'ActiveIncrease'=>$countryData[0]['ActiveIncrease'],
			//'RecentDate'=>substr($recentDate,0,10)
			'RecentDate'=>$recentDate,

		);

	}
}
else{
	$countryData= null;
	//나라 데이터가 없는 경우
	//세계면 나라별 / 미국이면 주별
	//데이터를 모두 sum 해서 recent 값 구한다

	$totalRecentDataSql="SELECT
  CountryRegion,
   Confirmed,
   Deaths,
   Recovered,
	 ActiveCases,";

	 if($country=='USA'){	$totalRecentDataSqlWhere="CountryRegion = 'USA' AND ProvinceState='' AND ";}
	 else{
	 	$totalRecentDataSqlWhere=  "CountryRegion LIKE '%Total%' AND ";
	 }

	 $totalRecentDataSql.="ActiveCases-(SELECT ActiveCases FROM CoronaData2
 	WHERE ".$totalRecentDataSqlWhere." DataDate=(".$yesterdaySql2."))AS ActiveCasesIncrease,";

	$totalRecentDataSql.="
NewCases AS ConfirmedIncrease,
NewDeaths AS DeathsIncrease,
NewRecovered AS RecoveredIncrease,
(".$yesterdaySql.") AS RecentDate
FROM CoronaData2
WHERE ";

	$totalRecentDataSql.=$totalRecentDataSqlWhere;

  $totalRecentDataSql.="DataDate =('".$today."')";

	$resultTotal = $conn->query($totalRecentDataSql);

	if ($resultTotal->num_rows > 0) {

		// output data of each row
		while($row = $resultTotal->fetch_assoc()) {
			$countryDataRecent= array(
				'Confirmed'=>$row['Confirmed'],
				'Active'=>$row['ActiveCases'],
				'Deaths'=>$row['Deaths'],
				'Recovered'=>$row['Recovered'],
				'Increase'=>$row['ConfirmedIncrease'],
				'DeathsIncrease'=>$row['DeathsIncrease'],
				'RecoveredIncrease'=>$row['RecoveredIncrease'],
				'ActiveIncrease'=>$row['ActiveCasesIncrease'],
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
