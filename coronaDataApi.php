
<?php

$servername = 'corona.cdvmwkpszam8.us-east-2.rds.amazonaws.com';
$username = 'root';
$password = 'samsamsam';
$dbname = 'corona';

	$conn = new mysqli($servername, $username, $password,$dbname);

if ($conn->connect_error) {
	die("Connection failed: ".$conn->connect_error);
}

// $provinceState = isset($_GET['provinceState'])&&$_GET['provinceState']!=''?$_GET['provinceState']:'';
// $countryRegion = isset($_GET['countryRegion'])&&$_GET['countryRegion']!=''?$_GET['countryRegion']:'';
//
//
//
// $sqlCountryList="SELECT provinceState,countryRegion,(SELECT confirmed FROM coronaData WHERE provinceState=CD.provinceState AND countryRegion=CD.countryRegion ORDER BY dataDate DESC LIMIT 1 ) AS cnt FROM coronaData AS CD GROUP BY provinceState,countryRegion
// ORDER BY cnt DESC ";
// $sqlCountyData = "SELECT provinceState,countryRegion,lastUpdate,confirmed,deaths,recovered,dataDate FROM coronaData WHERE provinceState = '".$provinceState."' AND countryRegion= '".$countryRegion."'
// ORDER BY dataDate DESC ";
//
//
// $result = $conn->query($sqlCountryList);
// if ($result->num_rows > 0) {
//
// 	while($row = $result->fetch_assoc()) {
//
// 		$countryList[] = array(
// 			'provinceState'=>$row['provinceState'],
// 			'countryRegion'=>$row['countryRegion'],
// 			'cnt'=>$row['cnt']
// 		);
// 	}
// }
//
//
// if($provinceState!=''||$countryRegion!=''){
//
// 	$result = $conn->query($sqlCountyData);
// 	if ($result->num_rows > 0) {
//
// 		while($row = $result->fetch_assoc()) {
//
// 			$countryData[] = array(
// 				'provinceState'=>$row['provinceState'],
// 				'countryRegion'=>$row['countryRegion'],
// 				'lastUpdate'=>$row['lastUpdate'],
// 				'confirmed'=>$row['confirmed'],
// 				'deaths'=>$row['deaths'],
// 				'recovered'=>$row['recovered'],
// 				'dataDate'=>$row['dataDate']
// 			);
// 		}
// 	}
// }
// else{
// 	$countryData= null;
// }
//
//
// $coronaArray = array();
// $coronaArray['countryData'] = $countryData;
// $coronaArray['countryList'] = $countryList;
//
//   $result = "success";
//   $reason ="success";
//   $loopArray = json_encode($coronaArray);
//   jsonMapper($result,$reason,$loopArray);
//
//
//   function jsonMapper($result,$reason,$loopArray){
//
// 		$loopArray = "
// 		{
// 		  \"result\":\"$result\",
// 		  \"reason\":\"$reason\",
// 		  \"data\":$loopArray
// 		 }
// 		";
//
// 		echo $loopArray;
// 	}
// 	function jsonDataMapper($result,$reason,$loopArray, $dataYn){
//
// 		$loopArray = "
// 		{
// 		  \"result\":\"$result\",
// 		  \"reason\":\"$reason\",
// 		  \"dataYn\":\"$dataYn\",
// 		  \"data\":$loopArray
// 		 }
// 		";
//
// 		echo $loopArray;
// 	}


?>
