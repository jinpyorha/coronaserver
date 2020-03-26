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
	}
}
else{
	$countryData= null;
	//나라 데이터가 없는 경우
	//세계면 나라별 / 미국이면 주별
	//데이터를 모두 sum 해서 recent 값 구한다
}
//#######api 제작###########
$coronaArray = array();
$coronaArray['countryData'] = $countryData;

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
