<?
//질병목록
//https://ko.wikipedia.org/wiki/%EC%A7%88%EB%B3%91_%EB%AA%A9%EB%A1%9D
//위의 링크에서 data 불러오기 
?>
<?php

	include('simple_html_dom.php');
   $ch = curl_init();
   //$ch2 = curl_init();

   $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/5';
   //질병목록
   //$url ='https://github.com/CSSEGISandData/COVID-19/tree/master/csse_covid_19_data/csse_covid_19_daily_reports';
	$url='https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_daily_reports/';
	$yesterday = date('m-d-Y',strtotime("-2 days"));
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
  
	
   $dom = new simple_html_dom();
	
   $dom->load($content);
   $temp=$dom->root->nodes;
  
   //var_dump($temp[0]->plaintext);
   //echo $temp[0]->plaintext;
	$temp = $temp[0]->plaintext;
	$temp = str_replace('"',"" ,$temp);
	$temp = str_replace(', ',"-" ,$temp);
	
	//echo $temp;exit;
	$temp  = explode(',' ,$temp);
	//var_dump($temp);
	//exit;
	$countryArray[] = array();
	$coronaData = array();
	for($i=0;$i<count($temp);$i++)
	{
		if($i!=0&&$i%5==0){
			//echo $temp[$i].'<br>';
			//$countryArray = array();
			$coronaDataTemp =array();
			$coronaDataTemp = explode(' ',$temp[$i]);
			
			array_push($coronaData,$coronaDataTemp[0]);	
			array_push($countryArray,$coronaData);	
			$coronaData=array();
			array_push($coronaData,$coronaDataTemp[1]);	
			
		}
		else{
			array_push($coronaData,$temp[$i]);	
		}
	}
	//var_dump($countryArray);

	$coronaArray[] = array();
	$coronaArrayTemp = array();
	for($i=2;$i<count($countryArray);$i++){

		//$str='';
		$coronaArrayTemp = array();

		for($k=0;$k<6;$k++){
		//	$str.=$countryArray[$i][$k].',';
			$coronaArrayTemp = array(
				'ProvinceState'=>$countryArray[$i][0],
				'CountryRegion'=>$countryArray[$i][1],
				'LastUpdate' =>$countryArray[$i][2],
				'Confirmed'=>$countryArray[$i][3],
				'Deaths'=>$countryArray[$i][4],
				'Recovered'=>$countryArray[$i][5],
			);
		}

		array_push($coronaArray,$coronaArrayTemp);
		//$str.='<br>';
		//echo $str;
		
		
	}
	//echo count($coronaData);

//#######api 제작###########

//  $info[$key] = $row[$key];
       
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