
<?php
//db 연결 시작
$servername = 'corona.cdvmwkpszam8.us-east-2.rds.amazonaws.com';
$username = 'root';
$password = 'samsamsam';
$dbname = 'corona';

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
	$sqlCountryList.=" WHERE CD.CountryRegion = '".$country."'";
}
$sqlCountryList.=" GROUP BY ProvinceState,CountryRegion
ORDER BY cnt DESC ";


$sqlCountyData = "SELECT ProvinceState AS PS ,CountryRegion AS CR ,LastUpdate,Confirmed,
Deaths,Recovered,DataDate AS DD,
Confirmed-(SELECT Confirmed FROM CoronaData WHERE DataDate < DD AND ProvinceState = PS AND CountryRegion = CR ORDER BY DataDate DESC LIMIT 1 )  AS Increase FROM CoronaData WHERE ProvinceState LIKE '%".substr($provinceState,0,16)."%' AND CountryRegion LIKE '%".substr($countryRegion,0,16)."%' ORDER BY DataDate DESC ";

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
if($provinceState!=''||$countryRegion!=''){

	$result = $conn->query($sqlCountyData);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			//DATA 읽어오기
			$countryData[] = array(
				'ProvinceState'=>$row['PS'],
				'CountryRegion'=>$row['CR'],
				'LastUpdate'=>$row['LastUpdate'],
				'Confirmed'=>$row['Confirmed'],
				'Deaths'=>$row['Deaths'],
				'Recovered'=>$row['Recovered'],
				'DataDate'=>$row['DD'],
				'Increase'=>$row['Increase']==null?0:(int)$row['Increase']
			);
		}
	}
}
else{
	$countryData= null;
}

//#######api 제작###########
$coronaArray = array();
$coronaArray['countryData'] = $countryData;
$coronaArray['countryList'] = $countryList;

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
