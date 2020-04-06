<?php
include_once('dbconnect.php');

$today = date('Y-m-d');
$yesterday = date('Y-m-d',strtotime("-1 days"));
$yesterday2 = date('Y-m-d',strtotime("-2 days"));

$yesterdaySql="SELECT DataDate FROM CoronaData2 ORDER BY DataDate DESC LIMIT 1";//최근일
$yesterdaySql2="SELECT DataDate FROM CoronaData2
GROUP BY DataDate
ORDER BY DataDate DESC
LIMIT 1,1";//전일

$provinceState = isset($_GET['ProvinceState'])&&$_GET['ProvinceState']!=''?$_GET['ProvinceState']:'';
$countryRegion = isset($_GET['CountryRegion'])&&$_GET['CountryRegion']!=''?$_GET['CountryRegion']:'';
$country=
isset($_GET['country'])&&$_GET['country']!=''?$_GET['country']:'';

$sqlCountryList ="SELECT
  ProvinceState,
  CountryRegion,
  Confirmed AS cnt,
  DataDate
FROM CoronaData2
";
if($country=='USA'){
  $sqlCountryList.="WHERE ProvinceState <> '' AND ProvinceState <> 'Total:' AND ProvinceState <> 'USA Total' AND CountryRegion = 'USA'";
}else{
  $sqlCountryList.="WHERE ProvinceState ='' AND CountryRegion <> 'Total:' AND CountryRegion <> 'World'";
}
$sqlCountryList.=" AND DataDate = (SELECT DataDate FROM CoronaData2 ORDER BY DataDate DESC LIMIT 1)
GROUP BY ProvinceState,CountryRegion
ORDER BY cnt DESC ";
//country List 가져오기
$result = $conn->query($sqlCountryList);
if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
    $tempStatus = 0;
    if($row['ProvinceState']==$provinceState&&$row['CountryRegion']==$countryRegion){
      $tempStatus=1;
    }
		//DATA 읽어오기
		$countryList[] = array(
			'ProvinceState'=>$row['ProvinceState'],
			'CountryRegion'=>$row['CountryRegion'],
			'cnt'=>$row['cnt'],
      'selectStatus'=>$tempStatus,
		);
	}
}

//#######api 제작###########
$coronaArray = array();
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
