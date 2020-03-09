<?
//질병목록
//https://ko.wikipedia.org/wiki/%EC%A7%88%EB%B3%91_%EB%AA%A9%EB%A1%9D
//위의 링크에서 data 불러오기
?>
<?php

	$day=1;
	include('simple_html_dom.php');
   $ch = curl_init(); // initiate
   //$ch2 = curl_init();

   $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/5';
   //질병목록
   //$url ='https://github.com/CSSEGISandData/COVID-19/tree/master/csse_covid_19_data/csse_covid_19_daily_reports';
	$url='https://github.com/CSSEGISandData/COVID-19/blob/master/csse_covid_19_data/csse_covid_19_daily_reports/'; //corona report
	$yesterday = date('m-d-Y',strtotime("-".$day." days")); // - 는 -1 day; 이틀 전 = -2days ; . = 더하기
	$url.=$yesterday.'.csv';

   curl_setopt($ch, CURLOPT_URL,  $url); //ch 라는 변수에 다 들어감
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
   curl_setopt($ch, CURLOPT_TIMEOUT, 10);
   curl_setopt($ch, CURLOPT_HEADER, false);
   curl_setopt($ch, CURLOPT_REFERER,  $url);
   curl_setopt($ch, CURLOPT_USERAGENT, $agent);
   $content = curl_exec($ch);
   curl_close($ch);


   $dom = new simple_html_dom();

   $dom->load($content);
    //$A_sitebody = $dom->find('span.mw-headline',0)->plaintext;
   $tempTr = $dom->find('tbody tr');
   $tempTh = $dom->find('thead tr th');

   $cntTr =  sizeof($tempTr);
   $cntTh =  sizeof($tempTh);


   $thArray = array();
   for($i=0;$i<$cntTh;$i++)
   {
		$tempThData = str_replace('/','',$tempTh[$i]->plaintext);
		$tempThData = str_replace(' ','',$tempThData );
	   array_push($thArray,$tempThData);
   }

	$count = 0;
	for($i=0;$i<$cntTr;$i++)
	{
		$coronaTemp = array();
		$first = $tempTr[$i]->find('td');
		$tdCnt = count($first);
		for($k=1;$k<$tdCnt;$k++){
			$coronaTemp[$thArray[$k-1]] = $first[$k]->plaintext;
			//$string .= $first[$k]->plaintext;
		}
		$countryArray[]=$coronaTemp;
	}

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
