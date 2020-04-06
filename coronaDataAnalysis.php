<?PHP
  include_once('dbconnect.php');

  $today = date('Y-m-d');
  $yesterday = date('Y-m-d',strtotime("-1 days"));
  $fiveDaysAgo = date('Y-m-d',strtotime("-5 days"));
/*한 나라의 최근 5일자 활성환자 증가율
$sql2="SELECT CountryRegion AS CR,ProvinceState AS PS , ActiveCases,DataDate AS DD ,
(ActiveCases-(SELECT ActiveCases FROM CoronaData2 WHERE CountryRegion=CR AND ProvinceState = PS AND DataDate<DD ORDER BY DataDate DESC LIMIT 1))/ActiveCases*100 AS ActiveCasesIncrease
FROM  CoronaData2
WHERE CountryRegion = 'USA' AND ProvinceState='' AND DataDate<'2020-04-01'
ORDER BY DataDate DESC
LIMIT 10";
*/
  $sql = "SELECT CR,PS,AVG(ActiveCasesIncrease) AS ACIavg FROM (
SELECT CountryRegion AS CR ,ProvinceState AS PS , ActiveCases,DataDate AS DD ,
(ActiveCases-(SELECT ActiveCases FROM CoronaData2 WHERE CountryRegion=CR AND ProvinceState=PS AND DataDate<DD ORDER BY DataDate DESC LIMIT 1))/ActiveCases*100 AS ActiveCasesIncrease
FROM  CoronaData2
WHERE DataDate>'".$fiveDaysAgo."' AND DataDate<'".$today."' AND ActiveCases>100 AND CountryRegion<>'Total:' AND CountryRegion<>'World'
AND CountryRegion<>'Diamond Princess'
ORDER BY DataDate DESC
) AS b
GROUP BY CR";

  $sqlAsc= $sql." ORDER BY ACIavg ASC LIMIT 5";

  $sqlDesc= $sql." ORDER BY ACIavg DESC LIMIT 5";

  $confirmedAsc = $conn->query($sqlAsc);

  $confirmedDesc = $conn->query($sqlDesc);

  $sql="SELECT CountryRegion,Deaths,Confirmed,Deaths/Confirmed*100 AS DeathPer FROM CoronaData2 WHERE DataDate = (SELECT DataDate FROM CoronaData2 ORDER BY DataDate DESC LIMIT 1) AND CountryRegion<>'World' AND ProvinceState=''
  AND Confirmed>1000 ";

  $sqlAsc= $sql." ORDER BY DeathPer ASC ,Confirmed DESC LIMIT 5";

  $sqlDesc= $sql." ORDER BY DeathPer DESC  LIMIT 5";

  $deathsAsc = $conn->query($sqlAsc);

  $deathsDesc = $conn->query($sqlDesc);

  if ($confirmedAsc->num_rows > 0) {
    // output data of each row
    while($row = $confirmedAsc->fetch_assoc()) {
      //echo $row['CR'].','.$row['ACIavg'].'<br>';
      $confirmedArrAsc[] = array(
        'ProvinceState'=>'',
        'CountryRegion'=>$row['CR'],
        'value'=>round($row['ACIavg'],2),
        'country'=>'global',
        'type'=>'',
      );
    }
  }
  if ($confirmedDesc->num_rows > 0) {
    // output data of each row
    while($row = $confirmedDesc->fetch_assoc()) {
      //echo $row['CR'].','.$row['ACIavg'].'<br>';
      $confirmedArrDesc[] = array(
        'ProvinceState'=>'',
        'CountryRegion'=>$row['CR'],
        'value'=>round($row['ACIavg'],2),
        'country'=>'global',
        'type'=>'',
      );
    }
  }
  if ($deathsAsc->num_rows > 0) {
    // output data of each row
    while($row = $deathsAsc->fetch_assoc()) {
      //echo $row['CR'].','.$row['ACIavg'].'<br>';
      $deathsArrAsc[] = array(
        'ProvinceState'=>'',
        'CountryRegion'=>$row['CountryRegion'],
        'value'=>round($row['DeathPer'],2),
        'country'=>'global',
        'type'=>'',
      );
    }
  }
  if ($deathsDesc->num_rows > 0) {
    // output data of each row
    while($row = $deathsDesc->fetch_assoc()) {
      //echo $row['CR'].','.$row['ACIavg'].'<br>';
      $deathsArrDesc[] = array(
        'ProvinceState'=>'',
        'CountryRegion'=>$row['CountryRegion'],
        'value'=>round($row['DeathPer'],2),
        'country'=>'global',
        'type'=>'',
      );
    }
  }

  //#######api 제작###########
  $coronaArray = array();
  $coronaArray['ConfirmedAsc'] = $confirmedArrAsc;
  $coronaArray['ConfirmedDesc'] = $confirmedArrDesc;
  $coronaArray['DeathsAsc'] = $deathsArrAsc;
  $coronaArray['DeathsDesc'] = $deathsArrDesc;

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
