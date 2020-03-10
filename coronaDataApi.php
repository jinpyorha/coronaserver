
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

$provinceState = isset($_GET['provinceState'])&&$_GET['provinceState']!=''?$_GET['provinceState']:'';
$countryRegion = isset($_GET['countryRegion'])&&$_GET['countryRegion']!=''?$_GET['countryRegion']:'';


//$sqlCountryList ="SELECT ProvinceState,CountryRegion,COUNT(Id)AS cnt FROM CoronaData GROUP BY ProvinceState,CountryRegion";
$sqlCountryList="SELECT provinceState,countryRegion,(SELECT confirmed FROM coronaData WHERE provinceState=CD.provinceState AND countryRegion=CD.countryRegion ORDER BY dataDate DESC LIMIT 1 ) AS cnt FROM coronaData AS CD GROUP BY provinceState,countryRegion
ORDER BY cnt DESC ";
$sqlCountyData = "SELECT provinceState AS PS ,countryRegion AS CR ,lastUpdate,confirmed,
deaths,recovered,dataDate AS DD,
confirmed-(SELECT confirmed FROM coronaData WHERE dataDate<DD AND provinceState = PS AND countryRegion = CR ORDER BY dataDate DESC LIMIT 1 )  AS increase FROM coronaData WHERE provinceState = '".$provinceState."' AND countryRegion= '".$countryRegion."'
ORDER BY dataDate DESC ";

//country List 가져오기
$result = $conn->query($sqlCountryList);
if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		//DATA 읽어오기
		//echo '<li>'.$row['ProvinceState'].','.$row['CountryRegion'].','.$row['cnt'].'</li>';
		$countryList[] = array(
			'provinceState'=>$row['provinceState'],
			'countryRegion'=>$row['countryRegion'],
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
				'provinceState'=>$row['PS'],
				'countryRegion'=>$row['CR'],
				'lastUpdate'=>$row['lastUpdate'],
				'confirmed'=>$row['confirmed'],
				'deaths'=>$row['deaths'],
				'recovered'=>$row['recovered'],
				'dataDate'=>$row['DD'],
				'increase'=>$row['increase']==null?0:(int)$row['increase']
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
