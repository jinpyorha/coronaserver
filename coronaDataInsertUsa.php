<?php
//질병목록
//https://ko.wikipedia.org/wiki/%EC%A7%88%EB%B3%91_%EB%AA%A9%EB%A1%9D
//위의 링크에서 data 불러오기
//에러 로그 출력
	error_reporting(E_ALL);

	ini_set("display_errors", 1);

	include_once('simple_html_dom.php');

   $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/5';

	$ch = curl_init();
	$url='https://www.worldometers.info/coronavirus/country/us/';
	$today = date('Y-m-d');
	$now = date('Y-m-d H:i:s');
	echo 'now : '.$now.'<br>';
//	$yesterday = date('Y-m-d',strtotime("-1 days"));
//	$yesterday2 = date('Y-m-d',strtotime("-".$day." days"));


	//$url.=$yesterday.'.csv';

   curl_setopt($ch, CURLOPT_URL,  $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
   curl_setopt($ch, CURLOPT_TIMEOUT, 10);
   curl_setopt($ch, CURLOPT_HEADER, false);
   curl_setopt($ch, CURLOPT_REFERER,  $url);
   curl_setopt($ch, CURLOPT_USERAGENT, $agent);
   $content = curl_exec($ch);
   curl_close($ch);

   //db 연결 시작
	$servername = "onesunny3.cafe24.com";
	$username = "onesunny3";
	$password = "gana8338";
	$dbname = "onesunny3";

	// Create connection
	$conn = new mysqli($servername, $username, $password,$dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	//echo "Connected successfully";
	//db 연결 끝
	$countryCntToday = 0;
	$sqlCountryCnt="SELECT COUNT(*) AS CountryCnt FROM CoronaData2
WHERE DataDate ='".$today."'
AND CountryRegion='USA' AND ProvinceState <>''";

	$result = $conn->query($sqlCountryCnt);
	$row = $result->fetch_assoc();
	$countryCntToday = $row['CountryCnt'];

	if($countryCntToday>0){$mode='update';}else{$mode='insert';}

   $dom = new simple_html_dom();

   $dom->load($content);

    //$A_sitebody = $dom->find('span.mw-headline',0)->plaintext;
   $tempTr = $dom->find('table[id=usa_table_countries_today] tbody tr');
   $tempTh = $dom->find('thead tr th');
   $tempTd = $dom->find('tbody tr td');

   $cntTr =  sizeof($tempTr);
   $cntTh =  sizeof($tempTh);
   $cntTd =  sizeof($tempTd);
   for($i=0;$i<$cntTr;$i++){
    $country = $tempTr[$i]->children(0)->plaintext;

    $totalCases = $tempTr[$i]->children(1)->plaintext;
    $totalCases = str_replace(',','',$totalCases);
    $totalCases=$totalCases==''||$totalCases==null?0:$totalCases;

    $newCases = $tempTr[$i]->children(2)->plaintext;
    $newCases = str_replace('+','',$newCases);
    $newCases = str_replace(',','',$newCases);
    $newCases = $newCases==''||$newCases==null?0:$newCases;

    $totalDeaths = $tempTr[$i]->children(3)->plaintext;
    $totalDeaths = str_replace(',','',$totalDeaths);
    $totalDeaths = $totalDeaths==''||$totalDeaths==null?0:$totalDeaths;

    $newDeaths = $tempTr[$i]->children(4)->plaintext;
    $newDeaths = str_replace('+','',$newDeaths);
    $newDeaths = str_replace(',','',$newDeaths);
    $newDeaths = $newDeaths==null||$newDeaths==''?0:$newDeaths;
/*
    $totalRecovered = $tempTr[$i]->children(5)->plaintext;
    $totalRecovered = str_replace(',','',$totalRecovered);
    $totalRecovered =$totalRecovered==null||$totalRecovered==''?0:$totalRecovered;*/

    $activeCases= $tempTr[$i]->children(5)->plaintext;
    $activeCases = str_replace(',','',$activeCases);
    $activeCases=$activeCases==''||$activeCases==null?0:$activeCases;
		/*
    $sqlRecover = "SELECT Recovered FROM CoronaData2 WHERE CountryRegion = 'USA' AND ProvinceState='".$country."' ORDER BY DataDate DESC LIMIT 1 ";
    $result = $conn->query($sqlRecover);
    $recoveredYesterday = 0;
    if ($result->num_rows > 0) {
    	// output data of each row
    	$row = $result->fetch_assoc();
      $recoveredYesterday = $row['Recovered']!=null||$row['Recovered']!=''?$row['Recovered']:0;
    }
    $newRecovered = ($totalRecovered-$recoveredYesterday);*/
if($mode=='insert'){
    $sql="INSERT INTO CoronaData2 (CountryRegion,ProvinceState,Confirmed,Deaths,WrittenAtUtc,DataDate,NewCases,ActiveCases,NewDeaths,LastUpdate)
VALUES ('USA','".$country."',".$totalCases.",".$totalDeaths.",'".$now."','".$today."',".$newCases.",".$activeCases.",".$newDeaths.",'".$now."')";
}else if($mode=='update'){
		$sql ="UPDATE CoronaData2 SET Confirmed=".$totalCases.",Deaths=".$totalDeaths.",WrittenAtUtc='".$now."',NewCases=".$newCases.",ActiveCases=".$activeCases.",NewDeaths=".$newDeaths." ,LastUpdate= '".$now."' WHERE CountryRegion = 'USA' AND ProvinceState='".$country."' AND DataDate='".$today."'";
}
  if ($conn->query($sql) === TRUE) {
  //    echo "New record created successfully";
		echo '#';
  } else {
//      echo "Error: " . $sql . "<br>" . $conn->error;
 	}

}
	echo '-finish-';
?>
