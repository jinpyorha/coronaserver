
<?php



	include('simple_html_dom.php');


   $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/5';

	for($day=1;$day<2;$day++){
	$ch = curl_init();
	$url='https://github.com/CSSEGISandData/COVID-19/blob/master/csse_covid_19_data/csse_covid_19_daily_reports/';
	$yesterday = date('m-d-Y',strtotime("-".$day." days"));
	$yesterday2 = date('Y-m-d',strtotime("-".$day." days"));

	$url.=$yesterday.'.csv';

   curl_setopt($ch, CURLOPT_URL,  $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
   curl_setopt($ch, CURLOPT_TIMEOUT, 10);
   curl_setopt($ch, CURLOPT_HEADER, false);
   curl_setopt($ch, CURLOPT_REFERER,  $url);
   curl_setopt($ch, CURLOPT_USERAGENT, $agent);
   $content = curl_exec($ch);
   curl_close($ch);

	 error_reporting(E_ALL);
	 ini_set("display_errors",1);

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


   $dom = new simple_html_dom();

   $dom->load($content);
    //$A_sitebody = $dom->find('span.mw-headline',0)->plaintext;
   $tempTr = $dom->find('tbody tr');
   $tempTh = $dom->find('thead tr th');

   $cntTr =  sizeof($tempTr);
   $cntTh =  sizeof($tempTh);

	$sqlColumn = '';

   $thArray = array();
   for($i=0;$i<$cntTh;$i++)
   {
	   array_push($thArray,$tempTh[$i]->plaintext);
	   $tempThData = str_replace('/','',$tempTh[$i]->plaintext);
		$tempThData = str_replace(' ','',$tempThData );
	   $sqlColumn.=$tempThData.',';
   }
   $sqlColumn.='writtenAtUtc,dataDate';

	$count = 0;
	for($i=0;$i<$cntTr;$i++)
	{
		$sql = 'INSERT INTO coronaData ('.$sqlColumn.') VALUES(';
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
