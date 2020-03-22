<?php
//질병목록
//https://ko.wikipedia.org/wiki/%EC%A7%88%EB%B3%91_%EB%AA%A9%EB%A1%9D
//위의 링크에서 data 불러오기
//에러 로그 출력
	error_reporting(E_ALL);

	ini_set("display_errors", 1);

	include_once('simple_html_dom.php');

   $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/5';

	for($day=1;$day<2;$day++){
	$ch = curl_init();
	$url='https://www.worldometers.info/coronavirus/country/us/';
//	$yesterday = date('m-d-Y',strtotime("-".$day." days"));
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

    $sql="Insert Into CoronaCountry (CountryRegion,ProvinceState) VALUES ('US','".$country."')";
    if ($conn->query($sql) === TRUE) {
      //echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
   }

exit;

   $thArray = array();
   for($i=0;$i<$cntTh;$i++)
   {
	   array_push($thArray,$tempTh[$i]->plaintext);
	   $tempThData = str_replace('/','',$tempTh[$i]->plaintext);
		$tempThData = str_replace(' ','',$tempThData );
	   $sqlColumn.=$tempThData.',';
   }
   $sqlColumn.='WrittenAtUtc,DataDate';
exit;
	$count = 0;
	for($i=0;$i<$cntTr;$i++)
	{
		$sql = 'INSERT INTO CoronaData ('.$sqlColumn.') VALUES(';
		$coronaTemp = array();
		$first = $tempTr[$i]->find('td');
		$tdCnt = count($first);
		for($k=1;$k<$tdCnt;$k++){
			$coronaTemp[$thArray[$k-1]] = $first[$k]->plaintext;
			$sql .= "'".$first[$k]->plaintext."'";
			$sql.=',';
		}
		$sql.='NOW(),"'.$yesterday2 .'")';
		$countryArray[]=$coronaTemp;

		if ($conn->query($sql) === TRUE) {
			//echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
}
exit;
//#######api 제작###########
$coronaArray = array();
$coronaArray['countryData'] = $countryArray;

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
