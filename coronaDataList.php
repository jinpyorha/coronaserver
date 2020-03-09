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
	$yesterday = date('m-d-Y',strtotime("-1 days"));
	$url.=$yesterday.'csv';

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

   $A_sitebody = $dom->find('div.repository-content ',0)->find('tr.js-navigation-item');
   

	$cnt =  sizeof($A_sitebody);
	echo $cnt;


	$string = '<ol>';
	$count = 0;
	for($i=0;$i<$cnt;$i++)
	{	
		//$url='';
		//$first = substr($A_sitebody[$i]->href,1);
		//$define = explode(' ' ,$first);
		//for($k=0;$k<sizeof($define);$k++)
		//{
		//	$url.= trim($define[$k]);
		//}
		
//		$first =("http://www.snuh.org/health/encyclo".$url);
		//if($A_sitebody[$i]->class=='mw-redirect'||$A_sitebody[$i]->class=='mw-disambig'||$first=='#'||$count>137)
	//	{
	//	}
	//	else
	//	{
			//$string .= '<li>'.$A_sitebody[$i]->plaintext.'>>> '.$first.'</li>';

			$string .= '<li>'.$A_sitebody[$i]->plaintext.'</li>';
		//	$count++;

	//	}
	}
	$string.='</ol>';
	echo $string;
	exit;


	$cnt =  sizeof($A_sitebody2);
	echo $cnt;

	$count = 0;
	for($i=0;$i<$cnt;$i++)
	{
		$first = substr($A_sitebody2[$i]->href,0,1);
		if($A_sitebody2[$i]->class=='mw-redirect'||$A_sitebody2[$i]->class=='mw-disambig'||$first=='#')
		{
		}
		else
		{
			$string .= '<li>'.$A_sitebody2[$i]->plaintext.'</li>';
			$count++;

		}
	}

	$cnt =  sizeof($A_sitebody3);
	echo $cnt;

	$count = 0;
	for($i=0;$i<$cnt;$i++)
	{
		$first = substr($A_sitebody3[$i]->href,0,1);
		if($A_sitebody3[$i]->class=='mw-redirect'||$A_sitebody3[$i]->class=='mw-disambig'||$first=='#')
		{
		}
		else
		{
			$string .= '<li>'.$A_sitebody3[$i]->plaintext.'</li>';
			$count++;

		}
	}



	$string .= '</ol>';
	echo $string;


?>