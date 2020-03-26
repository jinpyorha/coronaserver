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
  $type=
  isset($_GET['type'])&&$_GET['type']!=''?$_GET['type']:'';

  /*
  type=total
    country=USA
    country='' // global

  type!=total
    country=USA , ProvinceState=specific state
    country=specific country
  */

  if($type=='total'){
    if($country=='USA'){
      $totalRecentDataSqlWhere="WHERE CountryRegion='USA' AND ProvinceState LIKE '%Total%' ";
    }else{//country='' //global
      $totalRecentDataSqlWhere="WHERE CountryRegion LIKE '%Total%' AND ProvinceState ='' ";
    }
  }else{//type!=Total
    if($country=='USA'){ // country=USA , ProvinceState=specific state
      $totalRecentDataSqlWhere="WHERE CountryRegion='USA' AND ProvinceState ='".$provinceState."' ";
    }else{ //country=specific country
      $totalRecentDataSqlWhere="WHERE CountryRegion='".$countryRegion."' AND ProvinceState ='' ";
    }
  }

  $totalRecentDataSql="SELECT CountryRegion,Confirmed,ActiveCases,Deaths,Recovered,
NewCases,NewDeaths,NewRecovered,
ActiveCases-(SELECT ActiveCases
 FROM CoronaData2 ";
  $totalRecentDataSql .=$totalRecentDataSqlWhere;
  $totalRecentDataSql.="
ORDER BY DataDate DESC LIMIT 1,1) AS NewActive,
DataDate,WrittenAtUtc
 FROM CoronaData2 ";
 $totalRecentDataSql .=$totalRecentDataSqlWhere;
 $totalRecentDataSql .="ORDER BY DataDate DESC LIMIT 1 ";

 //country List 가져오기
 $result = $conn->query($totalRecentDataSql);
 if ($result->num_rows > 0) {
 	// output data of each row
 	$row = $result->fetch_assoc();
  $countryDataRecent= array(
    'Confirmed'=>$row['Confirmed'],
    'Active'=>$row['ActiveCases'],
    'Deaths'=>$row['Deaths'],
    'Recovered'=>$row['Recovered'],
    'DataDate'=>$row['DataDate'],
    'Increase'=>$row['NewCases'],
    'DeathsIncrease'=>$row['NewDeaths'],
    'RecoveredIncrease'=>$row['NewRecovered'],
    'ActiveIncrease'=>$row['NewActive'],
    'WrittenAtUtc'=>$row['WrittenAtUtc']
  );


 }


  //#######api 제작###########
  $coronaArray = array();
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
